<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up() {
        Schema::create("address_types", function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->boolean("protected")->default(false);
            $table->boolean("active")->default(true);
            $table->timestamps();
        });

        Schema::create("city_types", function (Blueprint $table) {
            $table->id();
            $table->string("name")->unique();
            $table->timestamps();
        });

        Schema::create("island_groups", function (Blueprint $table) {
            $table->id();
            $table->string("code", 10)->unique();
            $table->string("name");
            $table->timestamps();
        });

        Schema::create("regions", function (Blueprint $table) {
            $table->id();
            $table->string("code", 10)->unique();
            $table->string("name");
            $table->string("regionName");
            $table->string("islandGroupCode", 10);
            $table->string("psgc10DigitCode", 10)->nullable()->unique();
            $table->timestamps();

            $table->foreign("islandGroupCode")->references("code")->on("island_groups")->onDelete("restrict");
        });

        Schema::create("provinces", function (Blueprint $table) {
            $table->id();
            $table->string("code", 10)->unique();
            $table->string("name");
            $table->boolean("isDistrict")->default(false);
            $table->string("regionCode", 10);
            $table->string("islandGroupCode", 10);
            $table->string("psgc10DigitCode", 10)->nullable()->unique();
            $table->timestamps();

            $table->foreign("regionCode")->references("code")->on("regions")->onDelete("restrict");
            $table->foreign("islandGroupCode")->references("code")->on("island_groups")->onDelete("restrict");
        });

        Schema::create("cities", function (Blueprint $table) {
            $table->id();
            $table->string("code", 10)->unique();
            $table->string("name");
            $table->string("oldName")->nullable();
            $table->unsignedBigInteger("type_id");
            $table->boolean("isCapital")->default(false);
            $table->string("provinceCode", 10)->nullable();
            $table->string("regionCode", 10);
            $table->string("islandGroupCode", 10);
            $table->string("psgc10DigitCode", 10)->nullable()->unique();
            $table->timestamps();

            $table->foreign("type_id")->references("id")->on("city_types")->onDelete("restrict");
            $table->foreign("provinceCode")->references("code")->on("provinces")->onDelete("restrict");
            $table->foreign("regionCode")->references("code")->on("regions")->onDelete("restrict");
            $table->foreign("islandGroupCode")->references("code")->on("island_groups")->onDelete("restrict");
        });

        Schema::create("barangays", function (Blueprint $table) {
            $table->id();
            $table->string("code", 10)->unique();
            $table->string("name");
            $table->string("oldName")->nullable();

            $table->string("cityCode", 10);
            $table->string("provinceCode", 10)->nullable();
            $table->string("regionCode", 10);
            $table->string("islandGroupCode", 10);
            $table->string("psgc10DigitCode", 10)->nullable()->unique();

            $table->unsignedBigInteger("district")->nullable();
            $table->string("telephone_number")->nullable();
            $table->string("contact_number")->nullable();
            $table->decimal("lng", 20, 8)->nullable();
            $table->decimal("lat", 20, 8)->nullable();
            $table->integer("dru")->nullable();

            $table->timestamps();

            $table->foreign("cityCode")->references("code")->on("cities")->onDelete("restrict");
            $table->foreign("provinceCode")->references("code")->on("provinces")->onDelete("restrict");
            $table->foreign("regionCode")->references("code")->on("regions")->onDelete("restrict");
            $table->foreign("islandGroupCode")->references("code")->on("island_groups")->onDelete("restrict");
        });

        Schema::create("addresses", function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("addressable_id");
            $table->string("addressable_type");
            $table->foreignId("type_id")->nullable()->constrained("address_types")->onDelete("restrict");
            $table->string("location");
            $table->string("barangayCode", 10);
            $table->string("zipCode", 5)->nullable();
            $table->boolean("isMain")->default(false);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign("barangayCode")->references("code")->on("barangays")->onDelete("restrict");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down() {
        Schema::table("addresses", function (Blueprint $table) {
            $table->dropForeign(["type_id"]);
            $table->dropForeign(["barangayCode"]);
        });
        Schema::table("barangays", function (Blueprint $table) {
            $table->dropForeign(["cityCode"]);
            $table->dropForeign(["provinceCode"]);
            $table->dropForeign(["regionCode"]);
            $table->dropForeign(["islandGroupCode"]);
        });
        Schema::table("cities", function (Blueprint $table) {
            $table->dropForeign(["provinceCode"]);
            $table->dropForeign(["regionCode"]);
            $table->dropForeign(["islandGroupCode"]);
        });
        Schema::table("provinces", function (Blueprint $table) {
            $table->dropForeign(["regionCode"]);
            $table->dropForeign(["islandGroupCode"]);
        });
        Schema::table("regions", function (Blueprint $table) {
            $table->dropForeign(["islandGroupCode"]);
        });

        Schema::dropIfExists("addresses");
        Schema::dropIfExists("barangays");
        Schema::dropIfExists("cities");
        Schema::dropIfExists("provinces");
        Schema::dropIfExists("regions");
        Schema::dropIfExists("island_groups");
        Schema::dropIfExists("city_types");
        Schema::dropIfExists("address_types");
    }
};
