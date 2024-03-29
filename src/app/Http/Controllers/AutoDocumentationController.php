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
        return $autodoc->generate();
    }
    public function parse() {
        $autodoc = new AutoDocumentation();
        return $autodoc->parseRoutes();
    }
    public function preview() {
        $autodoc = new AutoDocumentation();
        return $autodoc->preview();
    }
}