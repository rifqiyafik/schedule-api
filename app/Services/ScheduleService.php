<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Employee;

class ScheduleService
{
    public function getShift(Employee $employee, string $targetDateStr)
    {
        $targetDate = Carbon::parse($targetDateStr)->startOfDay();
        $startDate  = Carbon::parse($employee->start_date)->startOfDay();

        $diffDays = $startDate->diffInDays($targetDate, false);

        if ($diffDays < 0) {
            return null;
        }

        // === PERBAIKAN LOGIKA DISINI ===

        // 26 Des itu hari Kamis
        // dayOfWeekIso returns: 1 (Senin) ... 7 (Minggu)
        // Ubah ke Index Array: 0 (Senin) ... 6 (Minggu)
        $startDayOffset = $startDate->dayOfWeekIso - 1;

        // Tambahkan offset hari ke selisih hari
        // 26 Des (Kamis) -> Offset 3.
        // Perhitungan dimulai seolah-olah dari Senin (23 Des).
        $totalDaysIndex = $diffDays + $startDayOffset;

        // Modulo dengan panjang pola
        $index = $totalDaysIndex % count($employee->pattern);

        return $employee->pattern[$index];
    }

    public function getScheduleRange($employees, $startDate, $endDate)
    {
        $result = [];

        foreach ($employees as $emp) {
            $scheduleObj = [];
            $current = Carbon::parse($startDate);
            $end     = Carbon::parse($endDate);

            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');

                // Panggil fungsi getShift di atas
                $shift = $this->getShift($emp, $dateStr);

                if ($shift) {
                    $scheduleObj[$dateStr] = $shift;
                }

                $current->addDay();
            }

            $result[] = [
                'id' => str_pad($emp->id, 3, '0', STR_PAD_LEFT),
                'name' => $emp->name,
                'schedule' => $scheduleObj
            ];
        }

        return $result;
    }
}
