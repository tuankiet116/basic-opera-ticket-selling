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
        Schema::create('discounts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("event_id");
            $table->unsignedBigInteger("ticket_class_id")->nullable();
            $table->string("discount_code")->uniqid();
            $table->string("discount_type");
            $table->string("note")->nullable();
            $table->integer("quantity");
            $table->date("start_date")->nullable();
            $table->date("end_date")->nullable();
            $table->float("price_discount")->nullable();
            $table->float("percentage_discount")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discounts');
    }
};
