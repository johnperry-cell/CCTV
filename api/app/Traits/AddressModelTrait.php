<?php

namespace App\Traits;
use Illuminate\Http\Request;

trait AddressModelTrait {
    use HasProgressTrait;
    public static function syncApi($sources = null) {
        $_sources = $sources ?? self::$sync_urls;
        foreach ($_sources as $key => $url) {
            $add = [];
            if (property_exists(static::class, "sync_additional")) {
                $add = self::$sync_additional[$key];
            }
            self::synchronize($url, self::$sync_uniques, self::$sync_fields, $add);
        }
        if (method_exists(static::class, "sync_fix")) {
            self::sync_fix();
        }
    }

    protected static function synchronize($url, $indices, $fields, $add = []) {
        $data = collect(json_decode(file_get_contents($url), true));
        $filtered = self::nullifyValues($data, $fields, $add);
        $name = self::getTableName();
        $progress = self::getProgressBar("Seeding $name", $filtered->count());
        $progress->setMessage("$url");

        foreach ($filtered->chunk(5000) as $chunk) {
            self::upsert($chunk->toArray(), $indices, $fields);
            $progress->advance($chunk->count());
        }
        $progress->finish();
    }

    protected static function nullifyValues($data, $fields, $add = []) {
        $nullable = ["oldName", "cityCode", "districtCode", "subMunicipalityCode", "municipalityCode", "districtCode", "provinceCode", "psgc10DigitCode"];
        $boolean = ["isCapital"];
        $filtered = $data->map(function ($item, $key) use ($nullable, $boolean, $fields, $add) {
            $fixed = collect($item)->map(function ($i, $k) use ($nullable, $boolean) {
                if (in_array($k, $nullable)) {
                    return trim($i) != "" ? $i : null;
                } elseif (in_array($k, $boolean)) {
                    return !!$i;
                } else {
                    return $i;
                }
            });
            if ($fixed->has("districtCode") && $fixed["districtCode"] != null && $fixed["provinceCode"] == null) {
                $fixed["provinceCode"] = $fixed["districtCode"];
            }
            if ($fixed->has("municipalityCode") && $fixed["municipalityCode"] != null && $fixed["cityCode"] == null) {
                $fixed["cityCode"] = $fixed["municipalityCode"];
            }
            if ($fixed->has("subMunicipalityCode") && $fixed["subMunicipalityCode"] != null && $fixed["cityCode"] == null) {
                $fixed["cityCode"] = $fixed["subMunicipalityCode"];
            }
            $fixed = $fixed->except(["districtCode", "municipalityCode", "subMunicipalityCode"]);
            return $fixed->merge($add);
        });
        return $filtered;
    }
}
