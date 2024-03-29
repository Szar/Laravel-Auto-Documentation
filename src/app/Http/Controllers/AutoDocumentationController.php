<?php

namespace SeacoastBank\AutoDocumentation\App\Http\Controllers;

//use Illuminate\Http\Request;
use SeacoastBank\AutoDocumentation\AutoDocumentation;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AutoDocumentationController extends Controller
{
    function __construct()
    {
        $this->autodoc = new AutoDocumentation();
    }
    public function generate() {
        return $this->autodoc->generate();
    }
    public function parse() {
        return $this->autodoc->parseRoutes();
    }
    public function preview() {
        return $this->autodoc->preview();
    }
}