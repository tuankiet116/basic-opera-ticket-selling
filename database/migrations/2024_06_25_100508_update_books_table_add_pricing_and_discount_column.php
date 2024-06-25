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
            $table->string("discount_code")->nullable()->index()->after("ticket_class_id");
            $table->float("discount_price")->default(0)->nullable()->after("ticket_class_id");
            $table->float("pricing")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table("books", function (Blueprint $table) {
            $table->dropColumn("discount_code");
            $table->dropColumn("discount_price");
            $table->dropColumn("pricing");
        });
    }
};
