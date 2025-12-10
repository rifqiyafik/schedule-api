<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\ScheduleService;
use App\Models\Employee;
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

    /** @test */
    public function logika_shift_ahmad_benar_sesuai_offset_kamis()
    {
        // Ahmad (Pola 7 Hari): P, P, S, S, M, M, L
        // Start: 26 Des 2024 (KAMIS).
        // Kamis = Index ke-3 = 'S'.

        $emp = Employee::create([
            'name' => 'Ahmad',
            'pattern' => ['P', 'P', 'S', 'S', 'M', 'M', 'L'],
            'start_date' => '2024-12-26'
        ]);

        // Tes Hari H (26 Des)
        $this->assertEquals('S', $this->service->getShift($emp, '2024-12-26'));

        // Tes Besoknya (27 Des - Jumat - M)
        $this->assertEquals('M', $this->service->getShift($emp, '2024-12-27'));

        // Tes Jangka Panjang (2 Juni 2025 - Senin - P)
        $this->assertEquals('P', $this->service->getShift($emp, '2025-06-02'));
    }

    /** @test */
    public function logika_looping_range_tanggal_benar()
    {
        $emp = Employee::create([
            'name' => 'Ahmad',
            'pattern' => ['P'],
            'start_date' => '2024-12-26'
        ]);

        $result = $this->service->getScheduleRange(
            collect([$emp]),
            '2025-01-01',
            '2025-01-03'
        );

        // Pastikan menghasilkan array untuk 3 hari
        $this->assertCount(3, $result[0]['schedule']);
        $this->assertArrayHasKey('2025-01-01', $result[0]['schedule']);
        $this->assertArrayHasKey('2025-01-03', $result[0]['schedule']);
    }
}
