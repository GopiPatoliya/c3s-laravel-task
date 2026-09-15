<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\LeadController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', function () {
    return redirect()->route('leads.index');
})->name('home');

Route::middleware('auth')->group(function () {
    Route::get('/leads/data', [LeadController::class, 'data'])->name('leads.data');
    Route::get('/leads/export', [LeadController::class, 'export'])->name('leads.export');
    Route::patch('/leads/{lead}/status', [LeadController::class, 'updateStatus'])->name('leads.updateStatus');
    Route::resource('leads', LeadController::class);
});
