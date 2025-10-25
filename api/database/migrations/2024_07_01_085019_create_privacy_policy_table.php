<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void {
        Schema::create("policies", function (Blueprint $table) {
            $table->id();
            $table->string("title")->nullable();
            $table->text("content")->nullable();
            $table->timestamps();
        });

        Schema::create("privacy_prompts", function (Blueprint $table) {
            $table->id();
            $table->text("content");
            $table->timestamps();
        });

        Schema::create("privacies", function (Blueprint $table) {
            $table->id();
            $table->foreignId("user_id")->constrained("users");
            $table->foreignId("prompt_id")->constrained("privacy_prompts");
            $table->dateTime("activated_at")->nullable()->default(null);
            $table->timestamps();
        });

        Schema::create("privacy_policies", function (Blueprint $table) {
            $table->id();
            $table->foreignId("privacy_id")->constrained("privacies");
            $table->foreignId("policy_id")->constrained("policies");
            $table->unsignedTinyInteger("order")->default(1);
            $table->boolean("collapsible")->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {
        Schema::table("privacy_policies", function (Blueprint $table) {
            $table->dropForeign(["privacy_id"]);
            $table->dropForeign(["policy_id"]);
        });

        Schema::table("privacies", function (Blueprint $table) {
            $table->dropForeign(["user_id"]);
            $table->dropForeign(["prompt_id"]);
        });

        Schema::dropIfExists("privacy_policies");
        Schema::dropIfExists("privacies");
        Schema::dropIfExists("privacy_prompts");
        Schema::dropIfExists("policies");
    }
};
