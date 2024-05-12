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
        Schema::table("books", function (Blueprint $table) {
            $table->unique(["event_id", "seat_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("books", function (Blueprint $table) {
            $table->dropForeign(["client_id"]);
            $table->dropForeign(["seat_id"]);
            $table->dropForeign(["event_id"]);
            $table->dropUnique(["event_id", "seat_id"]);
            $table->foreign("event_id")->references("id")->on("events");
            $table->foreign("client_id")->references("id")->on("clients");
            $table->foreign("seat_id")->references("id")->on("seats");
        });
    }
};
