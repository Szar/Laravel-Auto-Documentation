<?php

namespace SeacoastBank\AutoDocumentation\App\Http\Controllers;

//use Illuminate\Http\Request;
use SeacoastBank\AutoDocumentation\AutoDocumentation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoDocumentationController extends Controller
{
    public function home() {
        $autodoc = new AutoDocumentation();
        $data = $autodoc->main();

        return view('autodocumentation::index', compact('data'));
    }
    public function generate() {
        $autodoc = new AutoDocumentation();
        //return response(json_encode($routes), 200)->header('Content-Type', 'application/json');
        return $autodoc->generate();
        //return response(json_encode($data), 200)->header('Content-Type', 'application/json');
        //return view('autodocumentation::index', compact('data'));
    }
}