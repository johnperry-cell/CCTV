<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;

use App\Models\Log;

use App\Http\Requests\Logs\GetLogsRequest;

use App\Http\Resources\LogResource;

class LogController extends Controller {
    protected $DS = DIRECTORY_SEPARATOR;

    public function getLogSetup(GetLogsRequest $request) {
        $modules = Log::select("module")->distinct()->pluck("module");
        return [
            "levels" => trail()::$levels,
            "modules" => $modules,
        ];
    }

    public function getLogs(GetLogsRequest $request, $year, $month = 0, $day = 0) {
        $year = $year == "null" ? Carbon::now()->format("Y") : $year;
        $hasMonth = $month && ($month > 0 && $month <= 12);
        $hasDay = $day && $day > 0;
        $dateStr = $hasMonth ? $year . "-" . $month . "-" . ($hasDay ? $day : "01") : $year . "-07-01";

        $result = [];
        $summary = [];
        $sql = "";

        $count = 0;
        if ($hasDay) {
            $page = $request->input("page", 1);
            $limit = $request->input("limit", 100);
            $offset = $limit * ($page - 1);
            $orderBy = $request->input("orderBy", "time");
            $order = $request->input("order", "desc");
            $levelFilter = $request->input("levels", null);
            $moduleFilter = $request->input("modules", null);

            $grph = Log::whereDate("created_at", $dateStr)->select("level")->selectRaw("count(*) as total")->groupBy("level")->get();

            $summary = $this->summarize($grph);

            $logs = Log::whereDate("created_at", $dateStr)
                ->when(!!$levelFilter, function ($query) use ($levelFilter) {
                    $query->whereIn("level", $levelFilter);
                })
                ->when(!!$moduleFilter, function ($query) use ($moduleFilter) {
                    $query->whereIn("module", $moduleFilter);
                })
                ->when($orderBy == "time", function ($query) use ($order) {
                    $query->orderBy("created_at", $order);
                })
                ->when($orderBy == "level", function ($query) use ($order) {
                    $query->orderBy("level", $order);
                })
                ->when($orderBy == "module", function ($query) use ($order) {
                    $query->orderBy("module", $order);
                })
                ->offset($offset)
                ->limit($limit);

            $result = LogResource::collection($logs->get());
            $count = Log::whereDate("created_at", $dateStr)
                ->when($levelFilter, function ($query) use ($levelFilter) {
                    $query->whereIn("level", $levelFilter);
                })
                ->selectRaw("count(*) as count")
                ->first()->count;
            return response([
                "data" => $result,
                "count" => $count,
                "summary" => $summary,
            ]);
        } else {
            $date = Carbon::parse($dateStr);
            $start = ($hasMonth ? ($hasDay ? $date->startOfDay() : $date->startOfMonth()) : $date->startOfYear())->format("Y-m-d H:i:s");
            $end = ($hasMonth ? ($hasDay ? $date->endOfDay() : $date->endOfMonth()) : $date->endOfYear())->format("Y-m-d H:i:s");
            $sqlDateStr = $hasMonth ? "YYYY-MM-dd" : "YYYY-MM";
            $RawSqls = [
                "select" => "TO_CHAR(created_at::timestamp, '$sqlDateStr') as date",
                "group" => "TO_CHAR(created_at::timestamp, '$sqlDateStr')",
            ];

            $logs = Log::select("level", \DB::raw($RawSqls["select"]), \DB::raw("count(*) as total"))
                ->whereBetween("created_at", [$start, $end])
                ->groupBy("level", \DB::raw($RawSqls["group"]))
                ->orderBy("date", "desc")
                ->get();

            $summary = $this->summarize($logs);

            $logs
                ->groupBy("date")
                ->reverse()
                ->map(function ($item, $key) {
                    return $item->groupBy("level")->map(function ($items, $keys) {
                        return $items->first()->total;
                    });
                })
                // Fill missing levels
                ->each(function ($item, $key) use (&$result) {
                    collect(trail()::$levels)->each(function ($name, $level) use ($item, $key, &$result) {
                        $result[$key][$level] = $item[$level] ?? 0;
                    });
                });

            // Fille Missing Dates
            if (!$hasMonth) {
                for ($i = 1; $i <= 12; $i++) {
                    $date_str = $year . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
                    if (!collect($result)->has($date_str)) {
                        $result[$date_str] = [];
                        foreach (trail()::$levels as $keys => $values) {
                            $result[$date_str][$keys] = 0;
                        }
                    }
                    $result[$date_str]["total"] = array_sum($result[$date_str]);
                }
            } else {
                $mnth = Carbon::parse($dateStr);
                $mntResult = [];
                for ($i = 1; $i <= $mnth->daysInMonth; $i++) {
                    $date_str = $year . "-" . str_pad($month, 2, "0", STR_PAD_LEFT) . "-" . str_pad($i, 2, "0", STR_PAD_LEFT);
                    $mntResult[$date_str] = $result[$date_str] ?? [];
                    if (!collect($result)->has($date_str)) {
                        foreach (trail()::$levels as $keys => $values) {
                            $mntResult[$date_str][$keys] = 0;
                        }
                    }
                    $mntResult[$date_str]["total"] = array_sum($mntResult[$date_str]);
                }
                $result = $mntResult;
            }
        }

        return response([
            "data" => $result,
            "summary" => $summary,
        ]);
    }

    public function summarize($grph) {
        $summary = [];
        $total = 0;

        foreach (trail()::$levels as $keys => $values) {
            $tmp = $grph->filter(function ($value, $key) use ($keys) {
                return $keys == $value["level"];
            });
            if ($tmp->first()) {
                $summary[] = [
                    "total" => $tmp->sum("total"),
                    "prct" => 0,
                    "level" => $keys,
                    "name" => $values,
                ];
                $total += $tmp->first()->total;
            } else {
                $summary[] = [
                    "total" => 0,
                    "prct" => 0,
                    "level" => $keys,
                    "name" => $values,
                ];
            }
        }
        $summary = collect($summary)->map(function ($item, $key) use ($total) {
            return [
                "total" => $item["total"],
                "prct" => $total > 0 ? (float) number_format(($item["total"] / $total) * 100, 2) : 0,
                "level" => $item["level"],
                "name" => $item["name"],
            ];
        });
        return $summary->prepend([
            "total" => $total,
            "prct" => 100,
            "level" => 0,
            "name" => "Total",
        ]);
    }

    public function deleteLogFiles(GetLogsRequest $request) {
        $names = $request->input("names", []);

        collect($names)->each(function ($name) {
            $path = storage_path("logs" . $this->DS . $name);
            if (File::exists($path)) {
                File::delete($path);
            }
        });

        return $this->getLogFiles($request);
    }

    public function getLogFiles(GetLogsRequest $request) {
        $path = storage_path("logs");
        $files = collect(File::files($path))
            ->map(function ($item, $key) {
                return [
                    "name" => $item->getFilename(),
                    "size" => $item->getSize(),
                    "modified" => Carbon::parse($item->getMTime())->format("m/d/Y h:i A"),
                ];
            })
            ->sortByDesc("name", SORT_NATURAL)
            ->values()
            ->toArray();
        return response($files);
    }

    public function getSystemLog(GetLogsRequest $request, string $name = "laravel.log") {
        $path = storage_path("logs" . $this->DS . $name);
        if (File::exists($path)) {
            $size = File::size($path);
            if ($size > 52428800) {
                // if ($size > 1024 * 1024 * 3) {
                // 50MB
                return response(
                    [
                        "size" => $size,
                        "code" => "file_too_large",
                        "message" => "Log file is too large to preview, please download the file instead!",
                    ],
                    422
                );
            }
            $content = File::get($path);
            $mime = File::mimeType($path);
            $headers = ["Content-Type" => $mime, "Content-Length" => $size];
            return response($content)->header("Content-Type", $mime)->header("Content-Length", $size);
        }
        return response(
            [
                "message" => "Log file not found!",
                "code" => "file_not_found",
            ],
            422
        );
    }

    public function downloadLogFiles(GetLogsRequest $request) {
        $filesNames = $request->input("names", []);

        $paths = [];

        collect($filesNames)->each(function ($name) use (&$paths) {
            $path = storage_path("logs" . $this->DS . $name);
            if (File::exists($path)) {
                $paths[] = $path;
            }
        });

        if (count($paths) == 0) {
            return response(
                [
                    "message" => "Log file not found!",
                    "code" => "file_not_found",
                ],
                422
            );
        }

        if (count($paths) == 1) {
            return response()->download($paths[0]);
        }

        if (count($paths) > 1) {
            $zip = new \ZipArchive();
            $tmpPath = storage_path("logs" . $this->DS . "log" . now()->format("Y-m-d_H-i-s") . ".zip");
            $zip->open($tmpPath, \ZipArchive::CREATE | \ZipArchive::OVERWRITE);
            foreach ($paths as $path) {
                $zip->addFile($path, basename($path));
            }
            $zip->close();
            return response()->download($tmpPath)->deleteFileAfterSend(true);
        }
    }
}
