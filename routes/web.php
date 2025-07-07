<?php

use Illuminate\Support\Facades\Route;

/*Route::get('/', function () {
    return view('welcome');
});*/

Auth::routes();

Route::get('/', [App\Http\Controllers\HomeController::class, 'index']);

Route::middleware(['auth'])->group(function () {
    /*Home*/
    Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

    /*Calendar*/
    Route::get('/calendar', [App\Http\Controllers\CalendarController::class, 'index'])->name('calendar');

    /*Event*/
    Route::get('/events', [App\Http\Controllers\EventController::class, 'index']);
    Route::post('/events', [App\Http\Controllers\EventController::class, 'store']);
    Route::put('/events/{event}', [App\Http\Controllers\EventController::class, 'update']);
    Route::delete('/events/{event}', [App\Http\Controllers\EventController::class, 'destroy']);
});
