<?php
Route::prefix('auto-documentation')->group(function () {
    Route::get('/generate', 'AutoDocumentationController@generate');
    Route::get('/parse', 'AutoDocumentationController@parse');
    Route::get('/preview', 'AutoDocumentationController@preview');
});