<?php
//namespace SeacoastBank\AutoDocumentation\Controllers;
//namespace SeacoastBank\AutoDocumentation\routes;
//use SeacoastBank\AutoDocumentation\Controllers\AutoDocumentationController;
//protected $namespace = '';
//protected $namespace = 'SeacoastBank\AutoDocumentation\Controllers';
//use SeacoastBank\AutoDocumentation\Http;
//use Illuminate\Support\Facades\Route;
//namespace('\SeacoastBank\AutoDocumentation\Http')->
Route::prefix('auto-documentation')->group(function () {
   // Route::get('/', [Controller::class, 'home']);
   // Route::get('/laravel-package-example', 'AutoDocumentationController@index');
    Route::get('/generate', 'AutoDocumentationController@generate');
});