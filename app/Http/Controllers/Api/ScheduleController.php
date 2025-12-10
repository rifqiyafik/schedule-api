<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Employee;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\Validator;
use Exception;

class ScheduleController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Helper untuk validasi standar agar responnya seragam
     */
    private function validateRequest(Request $request, array $rules)
    {
        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            // Cek apakah error karena missing parameter atau format
            $errors = $validator->errors();
            $message = "Validation Error.";

            // Prioritaskan pesan error yang spesifik
            if ($errors->has('start_date') || $errors->has('end_date') || $errors->has('user_id') || $errors->has('date')) {
                $message = $errors->first(); // Ambil pesan error pertama
            }

            return response()->json([
                'status' => 'error',
                'message' => $message,
                'http_code' => 400
            ], 400);
        }

        return null; // Return null jika validasi sukses
    }

    public function index(Request $request)
    {
        // 1. Pengecekan Missing Parameter & Format & Date Logic
        $validationError = $this->validateRequest($request, [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'user_id'    => 'nullable'
        ]);

        if ($validationError) return $validationError;

        try {
            // 2. Pengecekan Parsing Gagal
            $startDate = Carbon::createFromFormat('Y-m-d', $request->start_date);
            $endDate   = Carbon::createFromFormat('Y-m-d', $request->end_date);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Date parsing failed. Ensure format is YYYY-MM-DD.',
                'http_code' => 400
            ], 400);
        }

        // 3. Pengecekan User Tidak Ditemukan
        if ($request->filled('user_id')) {
            // ltrim untuk handle jika user kirim '001' tapi di db '1'
            $userId = ltrim($request->user_id, '0');
            $employees = Employee::where('id', $userId)->get();

            if ($employees->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.',
                    'http_code' => 404
                ], 404);
            }
        } else {
            $employees = Employee::all();
        }

        $data = $this->scheduleService->getScheduleRange(
            $employees,
            $request->start_date,
            $request->end_date
        );

        return response()->json([
            'status' => 'success',
            'data'   => $data,
            'http_code' => 200
        ]);
    }

    public function exportCsv(Request $request)
    {
        // 1. Validasi
        $validationError = $this->validateRequest($request, [
            'start_date' => 'required|date_format:Y-m-d',
            'end_date'   => 'required|date_format:Y-m-d|after_or_equal:start_date',
            'user_id'    => 'nullable'
        ]);

        if ($validationError) return $validationError;

        // 2. Parsing Date (Handling Parsing Error)
        try {
            $start = Carbon::createFromFormat('Y-m-d', $request->start_date);
            $end   = Carbon::createFromFormat('Y-m-d', $request->end_date);
        } catch (Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Date parsing failed. Format mismatch.',
                'http_code' => 400
            ], 400);
        }

        // 3. User Check
        if ($request->filled('user_id')) {
            $userId = ltrim($request->user_id, '0');
            $employees = Employee::where('id', $userId)->get();

            if ($employees->isEmpty()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found.',
                    'http_code' => 404
                ], 404);
            }
        } else {
            $employees = Employee::all();
        }

        // Logic CSV
        $headers = ['ID', 'Nama'];
        $period = CarbonPeriod::create($start, $end);
        foreach ($period as $date) {
            $headers[] = $date->format('Y-m-d'); // Konsisten format
        }

        $callback = function () use ($employees, $period, $headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);

            foreach ($employees as $emp) {
                $row = [str_pad($emp->id, 3, '0', STR_PAD_LEFT), $emp->name];
                foreach ($period as $date) {
                    $row[] = $this->scheduleService->getShift($emp, $date->format('Y-m-d'));
                }
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=jadwal.csv",
        ]);
    }

    public function checkSchedule(Request $request)
    {
        // 1. Pengecekan Missing Parameter & Format
        $validationError = $this->validateRequest($request, [
            'user_id' => 'required',
            'date'    => 'required|date_format:Y-m-d'
        ]);

        if ($validationError) return $validationError;

        try {
            // 2. Pengecekan User Tidak Ditemukan
            $userId = ltrim($request->user_id, '0');
            $employee = Employee::where('id', $userId)->first();

            if (!$employee) {
                return response()->json([
                    "status"  => "error",
                    "message" => "User not found.",
                    "http_code" => 404
                ], 404);
            }

            // 3. Pengecekan Parsing Gagal
            try {
                $dateObj = Carbon::createFromFormat('Y-m-d', $request->date);
            } catch (Exception $e) {
                throw new Exception("Date parsing failed.");
            }

            // Logic utama
            $shift = $this->scheduleService->getShift($employee, $request->date);

            return response()->json([
                "status" => "success",
                "data" => [
                    "id"    => str_pad($employee->id, 3, '0', STR_PAD_LEFT),
                    "name"  => $employee->name,
                    "date"  => $request->date,
                    "shift" => $shift
                ],
                "http_code" => 200
            ]);
        } catch (Exception $e) {
            // Tangkap error umum atau error parsing yang dilempar ulang
            $message = $e->getMessage() === "Date parsing failed." ? "Date parsing failed." : "Internal Server Error";
            $code = $e->getMessage() === "Date parsing failed." ? 400 : 500;

            return response()->json([
                "status"  => "error",
                "message" => $message,
                "http_code" => $code
            ], $code);
        }
    }
}
