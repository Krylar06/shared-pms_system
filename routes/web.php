<?php

use App\Http\Controllers\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\DeviceController;

use App\Http\Controllers\Admin\CollegeController;
use App\Http\Controllers\Admin\OfficeController;
use App\Http\Controllers\Admin\StaffController;
use App\Http\Controllers\Admin\StaffDeviceController;

Route::resource('colleges', CollegeController::class)->names('admin.colleges');

Route::get('colleges/{college}/offices', [OfficeController::class, 'index'])->name('admin.offices.index');
Route::post('colleges/{college}/offices', [OfficeController::class, 'store'])->name('admin.offices.store');
Route::get('colleges/{college}/offices/{office}/edit', [OfficeController::class, 'edit'])->name('admin.offices.edit');
Route::put('colleges/{college}/offices/{office}', [OfficeController::class, 'update'])->name('admin.offices.update');
Route::delete('colleges/{college}/offices/{office}', [OfficeController::class, 'destroy'])->name('admin.offices.destroy');

Route::get('offices/{office}/staff', [StaffController::class, 'index'])->name('admin.staff.index');
Route::post('offices/{office}/staff', [StaffController::class, 'store'])->name('admin.staff.store');
Route::get('offices/{office}/staff/{staff}/edit', [StaffController::class, 'edit'])->name('admin.staff.edit');
Route::put('offices/{office}/staff/{staff}', [StaffController::class, 'update'])->name('admin.staff.update');
Route::delete('offices/{office}/staff/{staff}', [StaffController::class, 'destroy'])->name('admin.staff.destroy');

Route::get('staff/{staff}/devices', [StaffDeviceController::class, 'index'])->name('admin.staff.devices.index');
Route::post('staff/{staff}/devices/issue', [StaffDeviceController::class, 'issue'])->name('admin.staff.devices.issue');
Route::post('staff/{staff}/devices/{assignment}/return', [StaffDeviceController::class, 'return'])->name('admin.staff.devices.return');
Route::get('/login', [LoginController::class, 'show'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.submit');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/org-browser', 'admin.org-browser')->name('admin.org-browser');
    Route::view('/devices', 'admin.devices.index')->name('admin.devices.index');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->group(function () {
    Route::view('/dashboard', 'admin.dashboard')->name('admin.dashboard');
    Route::view('/org-browser', 'admin.org-browser')->name('admin.org-browser');

    Route::resource('/devices', DeviceController::class)->names('admin.devices');
    Route::put('devices/{device}/quick', [\App\Http\Controllers\Admin\DeviceController::class, 'quickUpdate'])
    ->name('admin.devices.quickUpdate');
    Route::view('/scanner', 'admin.scanner')->name('admin.scanner');
});

Route::get('/devices/{device}', [DeviceController::class, 'show'])->name('admin.devices.show');