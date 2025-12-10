<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Employee;
use App\Services\ScheduleService;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ScheduleServiceTest extends TestCase
{
    use RefreshDatabase;

    protected $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new ScheduleService();
    }

    // TEST 1: Cek Logika Modulo 7 Hari (Ahmad)
    public function test_calculate_shift_for_7_days_pattern()
    {
        // Pola Ahmad: P, P, S, S, M, M, L
        $emp = Employee::create([
            'name' => 'Ahmad',
            'pattern' => ['P', 'P', 'S', 'S', 'M', 'M', 'L'],
            'start_date' => '2024-12-26'
        ]);

        // Hari ke-0 (26 Des) -> Index 0 ('P')
        $this->assertEquals('P', $this->service->getShift($emp, '2024-12-26'));

        // Hari ke-6 (01 Jan 2025) -> Index 6 ('L')
        $this->assertEquals('L', $this->service->getShift($emp, '2025-01-01'));

        // Hari ke-7 (02 Jan 2025) -> Index 0 ('P') - Pola Berulang
        $this->assertEquals('P', $this->service->getShift($emp, '2025-01-02'));
    }

    // TEST 2: Cek Logika Modulo 14 Hari (Yohan)
    public function test_calculate_shift_for_14_days_pattern()
    {
        // Pola Yohan 14 Hari
        $pattern14 = ['L', 'P', 'P', 'P', 'S', 'S', 'P', 'L', 'S', 'S', 'P', 'S', 'S', 'P'];

        $emp = Employee::create([
            'name' => 'Yohan',
            'pattern' => $pattern14,
            'start_date' => '2024-12-26'
        ]);

        // Hari ke-0 -> Index 0 ('L')
        $this->assertEquals('L', $this->service->getShift($emp, '2024-12-26'));

        // Hari ke-13 (Akhir Pola) -> Index 13 ('P')
        // 26 Des + 13 hari = 8 Jan 2025
        $this->assertEquals('P', $this->service->getShift($emp, '2025-01-08'));

        // Hari ke-14 (Awal Pola Baru) -> Index 0 ('L')
        $this->assertEquals('L', $this->service->getShift($emp, '2025-01-09'));
    }
}
     