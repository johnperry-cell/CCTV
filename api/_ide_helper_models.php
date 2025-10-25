<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $addressable_id
 * @property string $addressable_type
 * @property int|null $type_id
 * @property string $location
 * @property string $barangayCode
 * @property string|null $zipCode
 * @property bool $isMain
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $addressable
 * @property-read \App\Models\Address\Barangay $barangay
 * @property-read string|null $hash
 * @property-read \App\Models\Address\AddressType|null $type
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereAddressableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereAddressableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereBarangayCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereIsMain($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereLocation($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address whereZipCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Address withoutTrashed()
 */
	class Address extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property bool $protected
 * @property bool $active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType whereActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType whereProtected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AddressType whereUpdatedAt($value)
 */
	class AddressType extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $oldName
 * @property string $cityCode
 * @property string|null $provinceCode
 * @property string $regionCode
 * @property string $islandGroupCode
 * @property string|null $psgc10DigitCode
 * @property int|null $district
 * @property string|null $telephone_number
 * @property string|null $contact_number
 * @property string|null $lng
 * @property string|null $lat
 * @property int|null $dru
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Address\City $city
 * @property-read string|null $hash
 * @property-read \App\Models\Address\IslandGroup $islandGroup
 * @property-read \App\Models\Address\Province|null $province
 * @property-read \App\Models\Address\Region $region
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereCityCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereContactNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereDru($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereIslandGroupCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereLat($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereLng($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereOldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay wherePsgc10DigitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereTelephoneNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Barangay whereUpdatedAt($value)
 */
	class Barangay extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string|null $oldName
 * @property int $type_id
 * @property bool $isCapital
 * @property string|null $provinceCode
 * @property string $regionCode
 * @property string $islandGroupCode
 * @property string|null $psgc10DigitCode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Barangay> $barangays
 * @property-read int|null $barangays_count
 * @property-read string|null $hash
 * @property-read \App\Models\Address\IslandGroup $islandGroup
 * @property-read \App\Models\Address\Province|null $province
 * @property-read \App\Models\Address\Region $region
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereIsCapital($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereIslandGroupCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereOldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereProvinceCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City wherePsgc10DigitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereTypeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|City whereUpdatedAt($value)
 */
	class City extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\City> $cities
 * @property-read int|null $cities_count
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|CityType whereUpdatedAt($value)
 */
	class CityType extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Barangay> $barangays
 * @property-read int|null $barangays_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\City> $cities
 * @property-read int|null $cities_count
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Province> $provinces
 * @property-read int|null $provinces_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Region> $regions
 * @property-read int|null $regions_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|IslandGroup whereUpdatedAt($value)
 */
	class IslandGroup extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property bool $isDistrict
 * @property string $regionCode
 * @property string $islandGroupCode
 * @property string|null $psgc10DigitCode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Barangay> $barangays
 * @property-read int|null $barangays_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\City> $cities
 * @property-read int|null $cities_count
 * @property-read string|null $hash
 * @property-read \App\Models\Address\IslandGroup $islandGroup
 * @property-read \App\Models\Address\Region $region
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereIsDistrict($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereIslandGroupCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province wherePsgc10DigitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereRegionCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Province whereUpdatedAt($value)
 */
	class Province extends \Eloquent {}
}

namespace App\Models\Address{
/**
 * 
 *
 * @property int $id
 * @property string $code
 * @property string $name
 * @property string $regionName
 * @property string $islandGroupCode
 * @property string|null $psgc10DigitCode
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Barangay> $barangays
 * @property-read int|null $barangays_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\City> $cities
 * @property-read int|null $cities_count
 * @property-read string|null $hash
 * @property-read \App\Models\Address\IslandGroup $islandGroup
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address\Province> $provinces
 * @property-read int|null $provinces_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereIslandGroupCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region wherePsgc10DigitCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereRegionName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Region whereUpdatedAt($value)
 */
	class Region extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppModel byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppModel newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppModel newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|AppModel query()
 */
	class AppModel extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $filable_id
 * @property string|null $filable_type
 * @property string $name
 * @property string|null $old_name
 * @property string $file_name
 * @property string $mime
 * @property string|null $ext
 * @property int $size
 * @property string $path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent|null $filable
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereExt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFilableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFilableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereFileName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereMime($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereOldName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File whereUpdatedAt($value)
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Profile> $profiles
 * @property-read int|null $profiles_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender withTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Gender withoutTrashed()
 */
	class Gender extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $file_id
 * @property string|null $alt
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\File $file
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereAlt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereFileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Image whereUpdatedAt($value)
 */
	class Image extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $actor
 * @property string $action
 * @property int $type
 * @property int $level
 * @property string|null $old_data
 * @property string|null $new_data
 * @property string $module
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereAction($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereActor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereModule($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereNewData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereOldData($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Log whereUserId($value)
 */
	class Log extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Collection<int, Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Permission withoutRole($roles, $guard = null)
 */
	class Permission extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string|null $title
 * @property string|null $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Policy whereUpdatedAt($value)
 */
	class Policy extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int $prompt_id
 * @property \Illuminate\Support\Carbon|null $activated_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Policy> $policies
 * @property-read int|null $policies_count
 * @property-read \App\Models\PrivacyPrompt $prompt
 * @property-read \App\Models\User $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy whereActivatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy wherePromptId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Privacy whereUserId($value)
 */
	class Privacy extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $content
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|PrivacyPrompt whereUpdatedAt($value)
 */
	class PrivacyPrompt extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $user_id
 * @property int|null $gender_id
 * @property string|null $first_name
 * @property string|null $middle_name
 * @property string|null $last_name
 * @property string|null $suffix
 * @property string|null $nickname
 * @property \Illuminate\Support\Carbon|null $birthdate
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Address> $addresses
 * @property-read int|null $addresses_count
 * @property-read \App\Models\Gender|null $gender
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Image> $images
 * @property-read int|null $images_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereBirthdate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereFirstName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereGenderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereLastName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereMiddleName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereNickname($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereSuffix($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Profile whereUserId($value)
 */
	class Profile extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $profile_id
 * @property int $image_id
 * @property bool $primary
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage wherePrimary($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage whereProfileId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ProfileImage whereUpdatedAt($value)
 */
	class ProfileImage extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $propable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prop byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prop newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prop newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Prop query()
 */
	class Prop extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $guard_name
 * @property bool $protected
 * @property string|null $color
 * @property int $level
 * @property string|null $description
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\User> $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role byHash(string $hash)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereColor($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereGuardName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereLevel($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereProtected($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Role withoutPermission($permissions)
 */
	class Role extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $username
 * @property string|null $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property int $fails
 * @property \Illuminate\Support\Carbon|null $disabled_at
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string|null $hash
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Profile|null $profile
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Role> $roles
 * @property-read int|null $roles_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Laravel\Sanctum\PersonalAccessToken> $tokens
 * @property-read int|null $tokens_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User byHash(string $hash)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDisabledAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereFails($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUsername($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 */
	class User extends \Eloquent {}
}

