<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create("logs", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->nullable()->constrained();
            $table->string("actor")->nullable();
            $table->longText("action");
            $table->tinyInteger("type"); // Action Type: 1 = Create, 2 = Update, 3 = Delete
            $table->tinyInteger("level");
            $table->json("old_data")->nullable();
            $table->json("new_data")->nullable();
            $table->string("module");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table("logs", function (Blueprint $table) {
            $table->dropForeign(["user_id"]);
        });
        Schema::dropIfExists("logs");
    }
};
