<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ResourceController;
use Illuminate\Support\Facades\Route;

Route::get('/', DashboardController::class)->name('dashboard');

Route::prefix('cadastros/{resource}')->name('resources.')->controller(ResourceController::class)->group(function (): void {
    Route::get('/', 'index')->name('index');
    Route::get('/novo', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}/editar', 'edit')->name('edit');
    Route::put('/{id}', 'update')->name('update');
    Route::delete('/{id}', 'destroy')->name('destroy');
});

Route::prefix('notas-fiscais')->name('invoices.')->controller(InvoiceController::class)->group(function (): void {
    Route::get('/', 'index')->name('index');
    Route::get('/nova', 'create')->name('create');
    Route::post('/', 'store')->name('store');
    Route::get('/{id}', 'show')->name('show');
});

Route::prefix('relatorios')->name('reports.')->controller(ReportController::class)->group(function (): void {
    Route::get('/', 'index')->name('index');
    Route::get('/{report}', 'show')->name('show');
});
