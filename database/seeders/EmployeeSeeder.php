<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Employee;

class EmployeeSeeder extends Seeder
{
    public function run()
    {
        // Tanggal mulai global sesuai soal
        $globalStart = '2024-12-26';

        $employees = [
            [
                'name' => 'Ahmad',
                'pattern' => ['P','P','S','S','M','M','L'], // 7 Hari
                'start_date' => $globalStart
            ],
            [
                'name' => 'Widi',
                'pattern' => ['S','S','M','M','L','P','S'], // 7 Hari
                'start_date' => $globalStart
            ],
            [
                'name' => 'Yono',
                'pattern' => ['M','M','P','L','P','P','M'], // 7 Hari
                'start_date' => $globalStart
            ],
            [
                'name' => 'Yohan',
                'pattern' => ['L','P','P','P','S','S','P','L','S','S','P','S','S','P'], // 14 Hari
                'start_date' => $globalStart
            ],
        ];

        foreach ($employees as $emp) {
            Employee::create($emp);
        }
    }
}
