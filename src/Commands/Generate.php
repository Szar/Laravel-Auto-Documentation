<?php
namespace SeacoastBank\AutoDocumentation\Commands;

use Illuminate\Console\Command;
use SeacoastBank\AutoDocumentation\AutoDocumentation;

class Generate extends Command
{
    protected $signature = 'docs:generate
    ';
    protected $description = 'Generates API documentation.';

    public function handle()
    {
        $autodoc = new AutoDocumentation;
        $autodoc->generate();
    }

}