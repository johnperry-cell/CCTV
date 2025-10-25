<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create("profile_images", function (Blueprint $table) {
            $table->id();
            $table->foreignId("profile_id")->constrained();
            $table->foreignId("image_id")->constrained();
            $table->boolean("primary")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table("profile_images", function (Blueprint $table) {
            $table->dropForeign(["profile_id"]);
            $table->dropForeign(["image_id"]);
        });
        Schema::dropIfExists("profile_images");
    }
};
