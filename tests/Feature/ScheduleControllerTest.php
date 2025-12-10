<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Employee;

class ScheduleControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed data minimal agar tes jalan
        Employee::create([
            'id' => 1,
            'name' => 'Ahmad',
            'pattern' => ['P'],
            'start_date' => '2024-12-26'
        ]);
    }

    /** @test */
    public function endpoint_list_jadwal_bisa_diakses()
    {
        $response = $this->getJson('/api/schedules?start_date=2025-01-01&end_date=2025-01-02');

        $response->assertStatus(200)
            ->assertJson(['status' => 'success']);
    }

    /** @test */
    public function endpoint_export_csv_menghasilkan_file_download()
    {
        $response = $this->get('/api/export-schedules?start_date=2025-01-01&end_date=2025-01-05');

        $response->assertStatus(200);

        // Cek Header HTTP apakah benar tipe file CSV
        $this->assertTrue($response->headers->contains('content-type', 'text/csv; charset=UTF-8'));
        $this->assertTrue($response->headers->contains('content-disposition', 'attachment; filename=jadwal.csv'));
    }

    /** @test */
    public function endpoint_cek_jadwal_satu_user_jalan()
    {
        $response = $this->getJson('/api/check-schedule?user_id=1&date=2025-01-01');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'status',
                'data' => ['shift']
            ]);
    }
}
