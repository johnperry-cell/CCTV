<?php

namespace App\Models\Address;

use App\Models\AppModel;

use App\Traits\AddressModelTrait;

class Province extends AppModel
{
    use AddressModelTrait;

    protected static $sync_urls = [
        'https://psgc.gitlab.io/api/provinces.json',
        'https://psgc.gitlab.io/api/districts.json',
    ];
    protected static $sync_uniques = ['code'];
    protected static $sync_fields = [
        "name", "isDistrict", "regionCode", "islandGroupCode", "psgc10DigitCode"
    ];
    protected static $sync_additional = [
        ['isDistrict' => false],
        ['isDistrict' => true],
    ];

    protected $fillable = [
        'code',
        'name',
        'isDistrict',
        'regionCode',
        'islandGroupCode',
        'psgc10DigitCode'
    ];

    protected $casts = [
        'isDistrict' => 'boolean',
    ];

    public function barangays(){
        return $this->hasMany(Barangay::class, 'provinceCode', 'code');
    }

    public function cities(){
        return $this->hasMany(City::class, 'provinceCode', 'code');
    }

    public function region(){
        return $this->belongsTo(Region::class, 'regionCode', 'code');
    }
    
    public function islandGroup(){
        return $this->belongsTo(IslandGroup::class, 'islandGroupCode', 'code');
    }
}
