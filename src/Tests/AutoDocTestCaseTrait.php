<?php

namespace SeacoastBank\AutoDocumentation\Tests;

use SeacoastBank\AutoDocumentation\Services\SwaggerService;
use SeacoastBank\AutoDocumentation\Http\Middleware\AutoDocumentationMiddleware;

trait AutoDocumentationTestCaseTrait
{
    protected $docService;

    public function setUp(): void
    {
        parent::setUp();

        $this->docService = app(SwaggerService::class);
    }

    public function createApplication()
    {
        parent::createApplication();
    }

    public function tearDown(): void
    {
        $currentTestCount = $this->getTestResultObject()->count();
        $allTestCount = $this->getTestResultObject()->topTestSuite()->count();

        if (($currentTestCount == $allTestCount) && (!$this->hasFailed())) {
            $this->docService->saveProductionData();
        }

        parent::tearDown();
    }

    /**
     * Disabling documentation collecting on current test
     */
    public function skipDocumentationCollecting()
    {
        AutoDocumentationMiddleware::$skipped = true;
    }
}
