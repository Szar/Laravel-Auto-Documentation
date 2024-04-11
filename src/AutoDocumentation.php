<?php

namespace SeacoastBank\AutoDocumentation;

use Illuminate\Support\Facades\Route;
use SeacoastBank\AutoDocumentation\Builder\Builder;
use SeacoastBank\AutoDocumentation\Parser\Parser;
use Illuminate\Support\Facades\Log;

class AutoDocumentation {

    private $builder;
    private $parser;

    function __construct($config = null) {
        $this->parser = new Parser();
        $this->builder = new Builder();
    }
    private function fetchRouteGroups() {
        $routeCollection = Route::getRoutes();
        $grouped_routes = array_filter($routeCollection->getRoutes(), function($route)  {
            $action = $route->getAction();
            if (isset($action['as'])) {
                return true;
            }
            return false;
        });
    }
    private function write($data, $path) {
        $f = fopen(base_path().$path, "w") or die("Unable to open file!");
        fwrite($f, $data);
        fclose($f);
        return true;
    }
    public function parseRoutes() {
        $data = [];
        foreach (Route::getRoutes() as $route) {
            if ($route->getActionName() !== 'Closure' && (strpos($route->uri, "api") !== false || $route->action["prefix"] === "api" || (array_key_exists("middleware", $route->action) && in_array("api", $route->action["middleware"])))) {
                $controllerName = !is_null($route->action['uses']) ? (is_array($route->action['uses']) ? $route->action['uses'][0] : explode('@', $route->action['uses']))[0] : null;
                $methodName = !is_null($route->action['uses']) ? (is_array($route->action['uses']) ? $route->action['uses'][1] : explode('@', $route->action['uses']))[1] : null;
                $class = new \ReflectionClass($controllerName);
                $method = $class->getMethod($methodName);
                try {
                    $doc = $this->parser->parse($class, $method);
                }
                catch(\Exception $e) {
                    $doc = false;
                    Log::debug("Error reading docblocks for {$controllerName} {$methodName}");
                    Log::debug($e);
                }
                $groups = explode('/', $route->getPrefix());
                if(!array_key_exists($groups[1], $data)) {
                    $data[$groups[1]] = [
                        'title' => ucfirst($groups[1]),
                        'data' => []
                    ];
                }
                if($doc) {
                    $data[$groups[1]]['data'][] = [
                        'name' =>  $route->getName(),
                        'method' => is_array($route->methods()) ? $route->methods()[0] : $route->methods(),
                        'class' => $class->getName(),
                        'function' => $method->getName(),
                        'uri' => "/".$route->uri,
                        'domain' => $groups[0],
                        'prefix' => $groups[1],
                        'uri_params' => preg_match_all("/{[^}]*}/", $route->uri, $uri_parameters),
                        'title' => $doc['summary'],
                        'description' => $doc['description'],
                        'parameters' => $doc['parameters'],
                        'response' => preg_replace( "/\n/", "\n        ", json_encode(json_decode($doc['response'], true), JSON_PRETTY_PRINT)),
                    ];
                }
            }
        }
        return $data;
    }
    public function generate() {
        $data = [
            'title' => 'API Reference Test Page',
            'description' => 'This is a test page!',
            'data' => $this->parseRoutes()
        ];
        $response = $this->builder->build($data);
        $this->write($response, "/docs/api.rst");
        return $response;
    }
    public function preview() {
        $parser = new \Gregwar\RST\Parser;
        $rst = $this->generate();
        return $parser->parse($rst);
    }
}