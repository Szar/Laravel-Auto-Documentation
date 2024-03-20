<?php
namespace SeacoastBank\AutoDocumentation\Controllers;

use Illuminate\Http\Request;
use SeacoastBank\AutoDocumentation\AutoDocumentation;

class AutoDocumentationController
{
    public function __invoke(AutoDocumentation $autodoc) {
        $response = $autodoc->main();

        return $response;
    }
    public function home(AutoDocumentation $autodoc) {
        $data = $autodoc->main();

        return view('autodocumentation::index', compact('data'));
    }
    public function generate(AutoDocumentation $autodoc) {

        //return response(json_encode($routes), 200)->header('Content-Type', 'application/json');
        return $autodoc->generate();
        //return response(json_encode($data), 200)->header('Content-Type', 'application/json');
        //return view('autodocumentation::index', compact('data'));
    }
}