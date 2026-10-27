<?php

use App\Http\Controllers\Api\TaskController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function (): void {
    Route::apiResource('tasks', TaskController::class)->whereNumber('task');
});
