<?php

namespace App\Models\Address;

use App\Models\AppModel;

class AddressType extends AppModel
{
    protected $fillable = [
        'name',
        'protected',
        'active',
    ];

    protected $casts = [
        'protected' => 'boolean',
        'active' => 'boolean',
    ];
}
