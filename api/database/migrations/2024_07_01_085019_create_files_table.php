<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create("files", function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger("filable_id")->nullable();
            $table->string("filable_type")->nullable();

            $table->string("name");
            $table->string("old_name")->nullable();
            $table->string("file_name");
            $table->string("mime");
            $table->string("ext")->nullable();
            $table->unsignedBigInteger("size");
            $table->string("path");

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::dropIfExists("files");
    }
};
