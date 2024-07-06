<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\InstanceController;


Route::get('/', function () {
    return view('welcome');
});

Route::resource('instances', InstanceController::class)->except(['show']);
Route::get('instances/running', [InstanceController::class, 'running'])->name('instances.running');
Route::get('instances/{instance}', [InstanceController::class, 'show'])->name('instances.show');
Route::post('instances/{instance}/start', [InstanceController::class, 'start'])->name('instances.start');
Route::post('instances/{instance}/stop', [InstanceController::class, 'stop'])->name('instances.stop');
Route::post('instances/{instance}/delete', [InstanceController::class, 'delete'])->name('instances.delete');
Route::post('instances/{instance}/restart', [InstanceController::class, 'restart'])->name('instances.restart');
Route::get('instances/{instance}/output', [InstanceController::class, 'output'])->name('instances.output');
