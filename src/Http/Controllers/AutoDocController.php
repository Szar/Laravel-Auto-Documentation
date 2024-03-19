<?php

namespace SeacoastBank\AutoDocumentation\Http\Controllers;

use Illuminate\Routing\Controller as BaseController;
use SeacoastBank\AutoDocumentation\Services\SwaggerService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AutoDocumentationController extends BaseController
{
    protected $service;

    public function __construct()
    {
        $this->service = app(SwaggerService::class);
    }

    public function documentation()
    {
        $documentation = $this->service->getDocFileContent();

        return response()->json($documentation);
    }

    public function index()
    {
        return view('auto-doc::documentation');
    }

    public function getFile($file)
    {
        $filePath = base_path("vendor/seacoastbank/auto-documentation/src/Views/swagger/{$file}");

        if (!file_exists($filePath)) {
            throw new NotFoundHttpException();
        }

        $content = file_get_contents($filePath);

        return response($content);
    }
}