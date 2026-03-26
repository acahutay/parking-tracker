<?php

use App\Http\Controllers\ParkingController;
use Illuminate\Support\Facades\Route;

Route::get('/sections', [ParkingController::class, 'sections']);
Route::get('/sections/available', [ParkingController::class, 'availableSections']);
Route::post('/sections/seed', [ParkingController::class, 'seedSections']);

Route::post('/check-in', [ParkingController::class, 'checkIn']);
Route::post('/check-out', [ParkingController::class, 'checkOut']);
Route::get('/tickets/active', [ParkingController::class, 'activeTickets']);
