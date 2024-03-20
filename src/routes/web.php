<?php

use SeacoastBank\AutoDocumentation\Controllers\AutoDocumentationController;
use Illuminate\Support\Facades\Route;

Route::prefix('auto-documentation')->group(function () {
    Route::get('/', [AutoDocumentationController::class, 'home']);
    Route::get('/generate', [AutoDocumentationController::class, 'generate']);
});