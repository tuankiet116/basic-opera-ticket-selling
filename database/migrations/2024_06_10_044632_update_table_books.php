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
        Schema::table('books', function (Blueprint $table) {
            $table->unsignedBigInteger("ticket_class_id")->after("seat_id")->nullable();
            $table->boolean("is_temporary")->default(false)->after("isPending");
            $table->string("token")->nullable()->after("is_temporary");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('ticket_class_id');
            $table->dropColumn('is_temporary');
            $table->dropColumn('token');
        });
    }
};
