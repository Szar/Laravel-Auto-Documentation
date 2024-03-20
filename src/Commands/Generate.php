<?php
namespace SeacoastBank\AutoDocumentation\Commands;

use Illuminate\Console\Command;
use SeacoastBank\AutoDocumentation\AutoDocumentation;

class Generate extends Command
{
    protected $signature = 'autodocumentation:generate
                            {--force : Force rewriting of existing routes}
    ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates application documentation.';

    /**
     * @var DocumentationConfig
     */
   // private $docConfig;

    /**
     * @var string
     */
    //private $baseUrl;
    public function handle(RouteMatcherInterface $routeMatcher)
    {
        $autodoc = new AutoDocumentation;
        // Using a global static variable here, so fuck off if you don't like it.
        // Also, the --verbose option is included with all Artisan commands.
        Flags::$shouldBeVerbose = $this->option('verbose');

        //$this->docConfig = new DocumentationConfig(config('autodocumentation'));
        //$this->baseUrl = $this->docConfig->get('base_url') ?? config('app.url');

        //URL::forceRootUrl($this->baseUrl);
        $autodoc->generate();
    }

}