<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstanceController;
use App\Http\Controllers\ScheduleController;
use App\Http\Controllers\DashboardController;

Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

Route::get('/instances', [InstanceController::class, 'index'])->name('instances.index');
Route::get('/instances/running', [InstanceController::class, 'running'])->name('instances.running');
Route::get('/instances/create', [InstanceController::class, 'create'])->name('instances.create');
Route::post('/instances', [InstanceController::class, 'store'])->name('instances.store');
Route::get('/instances/{instance}', [InstanceController::class, 'show'])->name('instances.show');
Route::post('/instances/{instance}/start', [InstanceController::class, 'start'])->name('instances.start');
Route::post('/instances/{instance}/stop', [InstanceController::class, 'stop'])->name('instances.stop');
Route::post('/instances/{instance}/restart', [InstanceController::class, 'restart'])->name('instances.restart');
Route::post('/instances/{instance}/delete', [InstanceController::class, 'delete'])->name('instances.delete');
Route::get('/instances/{instance}/output', [InstanceController::class, 'output'])->name('instances.output');
Route::get('/instances/{instance}/update', [InstanceController::class, 'showUpdatePage'])->name('instances.update.page');
Route::post('/instances/{instance}/check-updates', [InstanceController::class, 'checkUpdates'])->name('instances.check.updates');
Route::post('/instances/{instance}/confirm-updates', [InstanceController::class, 'confirmUpdates'])->name('instances.confirm.updates');
Route::get('/instances/{instance}/edit', [InstanceController::class, 'edit'])->name('instances.edit');
Route::post('/instances/{instance}', [InstanceController::class, 'update'])->name('instances.update');
Route::get('/instances/{instance}/env', [InstanceController::class, 'getEnv'])->name('instances.get.env');
Route::post('/instances/{instance}/env', [InstanceController::class, 'updateEnv'])->name('instances.update.env');
Route::get('instances/{instance}/schedules/create', [ScheduleController::class, 'create'])->name('schedules.create');
Route::post('instances/{instance}/schedules', [ScheduleController::class, 'store'])->name('schedules.store');
Route::get('schedules/{schedule}/edit', [ScheduleController::class, 'edit'])->name('schedules.edit');
Route::put('/schedules/{schedule}', [ScheduleController::class, 'update'])->name('schedules.update');
Route::delete('/schedules/{schedule}', [ScheduleController::class, 'destroy'])->name('schedules.destroy');
Route::post('schedules/{schedule}/trigger-now', [ScheduleController::class, 'triggerNow'])->name('schedules.triggerNow');

