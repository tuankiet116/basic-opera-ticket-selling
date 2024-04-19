<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table("clients", function(Blueprint $table) {
            $table->unsignedBigInteger("event_id")->after("id")->index()->nullable();
            $table->index("isSpecial");
            $table->foreign("event_id")->references("id")->on("events");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("clients", function(Blueprint $table) {
            $table->dropForeign(["event_id"]);
            $table->dropIndex(["isSpecial"]);
            $table->dropColumn("event_id");
        });
    }
};
