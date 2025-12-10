<?php

use App\Http\Controllers\Api\ScheduleController;
use Illuminate\Support\Facades\Route;

Route::get('/schedules', [ScheduleController::class, 'index']);
Route::get('/export-schedules', [ScheduleController::class, 'exportCsv']);
Route::get('/check-schedule', [ScheduleController::class, 'checkSchedule']);
