<?php

use SeacoastBank\AutoDocumentation\Http\Controllers\AutoDocumentationController;


Route::get('/auto-doc/documentation', ['uses' => AutoDocumentationController::class . '@documentation']);
Route::get('/auto-doc/{file}', ['uses' => AutoDocumentationController::class . '@getFile']);
Route::get(config('auto-doc.route'), ['uses' => AutoDocumentationController::class . '@index']);
