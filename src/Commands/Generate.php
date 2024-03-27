<?php
namespace SeacoastBank\AutoDocumentation\Commands;

use Illuminate\Console\Command;
use SeacoastBank\AutoDocumentation\AutoDocumentation;

class Generate extends Command
{
    protected $signature = 'docs:generate
                            {--force : Force rewriting of existing routes}
    ';
    protected $description = 'Generates API documentation.';

    public function handle()
    {
        $autodoc = new AutoDocumentation;
        $autodoc->generate();
    }

}