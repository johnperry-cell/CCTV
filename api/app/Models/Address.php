<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;

use App\Models\Address\AddressType;
use App\Models\Address\Barangay;

class Address extends AppModel
{
    use SoftDeletes;

    protected $fillable = [
        'addressable_id',
        'addressable_type',
        'location',// House No./Lot/Block No./Street/Alley/Etc...
        'type_id',
        'barangayCode',
        'zipCode',
        'isMain',
    ];

    protected $casts = [
        'isMain' => 'boolean'
    ];

    public function addressable(): MorphTo{
        return $this->morphTo();
    }

    public function type(){
        return $this->belongsTo(AddressType::class, 'type_id');
    }

    public function barangay(){
        return $this->belongsTo(Barangay::class, 'barangayCode', 'code');
    }

    public function city(){
        return $this->barangay->city();
    }

    public function province(){
        return $this->barangay->province();
    }

    public function region(){
        return $this->barangay->region();
    }

    public function islandGroup(){
        return $this->barangay->islandGroup();
    }
}
