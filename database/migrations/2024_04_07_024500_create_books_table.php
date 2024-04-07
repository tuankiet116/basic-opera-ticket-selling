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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("event_id");
            $table->unsignedBigInteger("client_id")->nullable();
            $table->unsignedBigInteger("seat_id");
            $table->boolean("isBooked")->default(false);
            $table->boolean("isPending")->default(false);
            $table->dateTime("start_pending")->nullable();
            $table->timestamps();

            $table->foreign("event_id")->references("id")->on("events");
            $table->foreign("client_id")->references("id")->on("clients");
            $table->foreign("seat_id")->references("id")->on("seats");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
