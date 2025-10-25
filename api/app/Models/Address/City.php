<?php

namespace App\Models\Address;

use App\Models\AppModel;

use App\Traits\AddressModelTrait;

class City extends AppModel {
    use AddressModelTrait;

    protected static $sync_urls = [
        "https://psgc.gitlab.io/api/cities.json",
        "https://psgc.gitlab.io/api/municipalities.json",
        "https://psgc.gitlab.io/api/sub-municipalities.json",
    ];
    protected static $sync_uniques = ["code"];
    protected static $sync_fields = [
        "name",
        "oldName",
        "isCapital",
        "provinceCode",
        "regionCode",
        "islandGroupCode",
        "psgc10DigitCode",
    ];
    protected static $sync_additional = [
        ["type_id" => 1], // City
        ["type_id" => 2], // Municipality
        ["type_id" => 3], // Sub-Municipality
    ];

    protected $fillable = [
        "code",
        "name",
        "oldName",
        "isMunicipality",
        "isCapital",
        "provinceCode",
        "regionCode",
        "islandGroupCode",
        "psgc10DigitCode",
    ];

    protected $casts = [
        "isMunicipality" => "boolean",
        "isCapital" => "boolean",
    ];

    protected static function sync_fix() {
        $fix = [
            // City of Cotabato
            "129804000" => [
                "provinceCode" => "124700000", // Province of Cotabato
            ],
            // City of Isabela
            "099701000" => [
                "provinceCode" => "150700000", // Basilan
            ],
        ];

        foreach ($fix as $key => $province) {
            $toFix = self::where("code", $key)->update($province);
        }

        $skip = [
            "112317000", // Island Garden City of Samal
            "034917000", // Science City of Muñoz
        ];
        $name_fix = self::where("name", "ilike", "%City%")
            ->whereNotIn("code", $skip)
            ->get();

        foreach ($name_fix as $key => $city) {
            $fixed = str_replace("City of ", "", $city["name"]);
            $fixed = str_replace(" City", "", $fixed);
            $city->update([
                "name" => $fixed,
            ]);
        }

        $cities = self::where("type_id", 1)
            ->whereNotIn("code", $skip)
            ->get();
        foreach ($cities as $key => $city) {
            $city->update([
                "name" => $city->name . " City",
            ]);
        }
    }

    public function barangays() {
        return $this->hasMany(Barangay::class, "cityCode", "code");
    }

    public function province() {
        return $this->belongsTo(Province::class, "provinceCode", "code");
    }

    public function region() {
        return $this->belongsTo(Region::class, "regionCode", "code");
    }

    public function islandGroup() {
        return $this->belongsTo(IslandGroup::class, "islandGroupCode", "code");
    }
}
