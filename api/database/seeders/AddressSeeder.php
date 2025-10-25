<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use App\Models\Address\CityType;
use App\Models\Address\AddressType;
use App\Models\Address\IslandGroup;
use App\Models\Address\Region;
use App\Models\Address\Province;
use App\Models\Address\City;
use App\Models\Address\Barangay;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $DS = DIRECTORY_SEPARATOR;
        $DB_PATH = 'seeders'.$DS.'address'.$DS;
        $now = now();
        $cityTypes = [
            ['name' => 'City', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Municipality', 'created_at' => $now, 'updated_at' => $now],
            ['name' => 'Sub Municipality', 'created_at' => $now, 'updated_at' => $now],
        ];

        $addressTypes = [
            ['name' => 'Residential', 'protected' => true, 'created_at' => $now, 'updated_at' => $now ],
            // ['name' => 'Home', 'protected' => true, 'created_at' => $now, 'updated_at' => $now ],
            // ['name' => 'Permanent', 'protected' => true, 'created_at' => $now, 'updated_at' => $now ],
            // ['name' => 'Provincial', 'protected' => true, 'created_at' => $now, 'updated_at' => $now ],
            // ['name' => 'City', 'protected' => true, 'created_at' => $now, 'updated_at' => $now ],
        ];

        CityType::insert($cityTypes);
        AddressType::insert($addressTypes);

        // Avoid changing the order of these models because doing so could cause foreign key restrictions to fail.
        IslandGroup::syncApi([
            database_path($DB_PATH.'island-groups.json'),
        ]);
        Region::syncApi([
            database_path($DB_PATH.'regions.json'),
        ]);
        Province::syncApi([
            database_path($DB_PATH.'provinces.json'),
            database_path($DB_PATH.'districts.json'),
        ]);
        City::syncApi([
            database_path($DB_PATH.'cities.json'),
            database_path($DB_PATH.'municipalities.json'),
            database_path($DB_PATH.'sub-municipalities.json'),
        ]);
        Barangay::syncApi([
            database_path($DB_PATH.'barangays.json'),
        ]); 
    }
}
