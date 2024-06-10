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
        Schema::table('event_seat_class', function(Blueprint $table) {
            $table->dropForeign(["event_id"]);
            $table->dropForeign(["seat_id"]);
            $table->dropForeign(["ticket_class_id"]);
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->dropForeign(["event_id"]);
        });

        Schema::table("books", function(Blueprint $table) {
            $table->dropForeign(["event_id"]);
            $table->dropForeign(["client_id"]);
            $table->dropForeign(["seat_id"]);
        });

        Schema::table("ticket_classes", function (Blueprint $table) {
            $table->dropForeign(["event_id"]);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('event_seat_class', function (Blueprint $table) {
            $table->foreign("ticket_class_id")->references("id")->on("ticket_classes");
            $table->foreign("seat_id")->references("id")->on("seats");
            $table->foreign("event_id")->references("id")->on("events");
        });

        Schema::table('clients', function (Blueprint $table) {
            $table->foreign("event_id")->references("id")->on("events");
        });

        Schema::table("books", function (Blueprint $table) {
            $table->foreign("event_id")->references("id")->on("events");
            $table->foreign("client_id")->references("id")->on("clients");
            $table->foreign("seat_id")->references("id")->on("seats");
        });

        Schema::table("ticket_classes", function (Blueprint $table) {
            $table->foreign('event_id')->references('id')->on('events');
        });
    }
};
