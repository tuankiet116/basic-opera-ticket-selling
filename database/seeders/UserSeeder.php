<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (config("app.env") == "development") {
            DB::table('users')->insert([
                "name" => "Admin",
                "email" => "development@gmail.com",
                "password" => bcrypt("development@123")
            ]);
        } else {
            DB::table('users')->insert([
                "name" => "Admin",
                "email" => "live.hoguomopera@gmail.com",
                "password" => bcrypt("FourSeasons@Hoguom.Opera")
            ]);
        }
    }
}
