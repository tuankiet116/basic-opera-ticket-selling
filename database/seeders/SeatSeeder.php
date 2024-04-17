<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeatSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $dataHall1 = $this->generateDataSeats(1, CONFIG_HALL_1);
        $dataHall2 = $this->generateDataSeats(2, CONFIG_HALL_2);
        array_push($dataHall1, ...$dataHall2);
        DB::table("seats")->insert($dataHall1);
    }

    private function generateDataSeats($hallName, $config): array
    {
        $result = [];
        foreach ($config as $data) {
            $seats = [];
            for ($i = $data["start"]; $i <= $data["end"]; $i++) {
                array_push($seats, [
                    "name" => $data["row"] . $i,
                    "row" => $data["row"],
                    "hall" => $hallName
                ]);
            }
            array_push($result, ...$seats);
        }
        return $result;
    }
}
