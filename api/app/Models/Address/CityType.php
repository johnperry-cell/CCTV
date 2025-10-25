<?php

namespace App\Models\Address;

use App\Models\AppModel;

class CityType extends AppModel
{
    protected $fillable = [
        'name'
    ];

    public function cities(){
        return $this->hasMany(City::class, 'type_id');
    }
}
