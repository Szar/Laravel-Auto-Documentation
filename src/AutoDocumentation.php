<?php

namespace SeacoastBank\AutoDocumentation;

use Illuminate\Support\Facades\Route;
//use SeacoastBank\AutoDocumentation\Lib\DocumentationConfig;
//use SeacoastBank\AutoDocumentation\Lib\Utils;
//use phpDocumentor\Reflection\DocBlockFactory;
//use phpDocumentor\Reflection\File\LocalFile;
//use phpDocumentor\Reflection\Php\Namespace_;
//use phpDocumentor\Reflection\Php\NodesFactory;
//use phpDocumentor\Reflection\Php\Project;
//use phpDocumentor\Reflection\Php\ProjectFactory;
//use phpDocumentor\Reflection\Php\Factory;
//use SeacoastBank\AutoDocumentation\Lib\ClassFileBuilder;
//use SeacoastBank\AutoDocumentation\Lib\InterfaceFileBuilder;
//use SeacoastBank\PHPDocToRst\Builder\MainIndexBuilder;
//use SeacoastBank\PHPDocToRst\Builder\NamespaceIndexBuilder;
//use SeacoastBank\PHPDocToRst\Extension\Extension;
//use SeacoastBank\PHPDocToRst\Builder\ClassFileBuilder;
//use SeacoastBank\PHPDocToRst\Builder\InterfaceFileBuilder;
//use phpDocumentor\Reflection\PrettyPrinter;
//use PhpParser\PrettyPrinter\Standard as PrettyPrinter;
use ReflectionClass;
use ReflectionMethod;
use Illuminate\Support\Facades\Log;
use Jasny\PhpdocParser\PhpdocParser as DocParser;
use Jasny\PhpdocParser\Set\PhpDocumentor;
use SeacoastBank\AutoDocumentation\Builder\Builder;
use SeacoastBank\AutoDocumentation\Parser\Parser;

class AutoDocumentation {

    private $builder;
    private $parser;

    function __construct($config = null) {

        $this->parser = new \SeacoastBank\AutoDocumentation\Parser\Parser();
        $this->builder = new \SeacoastBank\AutoDocumentation\Builder\Builder();


        //$this->docBuilder = new ApiDocBuilder(app_path(), base_path().'/doctest');
        /*$this->config = $config ?: new DocumentationConfig(config('apidocumentation'));
        $this->strategies = [
            'metadata' => [
                \SeacoastBank\ApiDocumentation\Strategies\Metadata\GetFromDocBlocks::class,
            ],
            'urlParameters' => [
                \SeacoastBank\ApiDocumentation\Strategies\UrlParameters\GetFromUrlParamTag::class,
            ],
            'queryParameters' => [
                \SeacoastBank\ApiDocumentation\Strategies\QueryParameters\GetFromQueryParamTag::class,
            ],
            'headers' => [
                \SeacoastBank\ApiDocumentation\Strategies\RequestHeaders\GetFromRouteRules::class,
            ],
            'bodyParameters' => [
                \SeacoastBank\ApiDocumentation\Strategies\BodyParameters\GetFromBodyParamTag::class,
            ],
            'responses' => [
                \SeacoastBank\ApiDocumentation\Strategies\Responses\UseTransformerTags::class,
                \SeacoastBank\ApiDocumentation\Strategies\Responses\UseResponseTag::class,
                \SeacoastBank\ApiDocumentation\Strategies\Responses\UseResponseFileTag::class,
                \SeacoastBank\ApiDocumentation\Strategies\Responses\UseApiResourceTags::class,
                \SeacoastBank\ApiDocumentation\Strategies\Responses\ResponseCalls::class,
            ],
        ];*/
    }
    public function fetchRoutes() {
        $response = [];
        $parser = new DocParser(PhpDocumentor::tags());
        foreach (Route::getRoutes() as $route) {
            if ($route->getActionName() !== 'Closure' && (strpos($route->uri, "api") !== false || $route->action["prefix"] === "api" || (array_key_exists("middleware", $route->action) && in_array("api", $route->action["middleware"])))) {
                $controllerName = !is_null($route->action['uses']) ? (is_array($route->action['uses']) ? $route->action['uses'][0] : explode('@', $route->action['uses']))[0] : null;
                $methodName = !is_null($route->action['uses']) ? (is_array($route->action['uses']) ? $route->action['uses'][1] : explode('@', $route->action['uses']))[1] : null;

                $route->name = $route->getName();
                $route->controller = new ReflectionClass($controllerName);
                $route->method = $route->controller->getMethod($methodName);

                $route->controller->docComments = $parser->parse($route->controller->getDocComment());
                $route->method->docComments = $parser->parse($route->method->getDocComment());

                preg_match_all("/{[^}]*}/", $route->uri, $uri_parameters);
                $route->uri_parameters = $uri_parameters;

                $route->comments = $this->parser->parse($route->method);

               // $cloningTraverser = new NodeTraverser([new CloningVisitor()]);
                //[$newPhpDocNode] = $cloningTraverser->traverse([$phpDocNode]);
                //$newPhpDocNode->getParamTagValues()[0]->type = new IdentifierTypeNode('Ipsum');


                
                //$factory  = \phpDocumentor\Reflection\DocBlockFactory::createInstance();
                //$docblock = $factory->create($comment);


                $response[] = $route;

                //$builder = new InterfaceFileBuilder($file, $interface, $this->extensions);
                //$builder->getContent();
                /*;
            $projectFactory = new ProjectFactory([
                new Factory\Argument(new PrettyPrinter()),
                new Factory\Class_(),
                new Factory\Constant(new PrettyPrinter()),
                new Factory\DocBlock(DocBlockFactory::createInstance()),
                new Factory\File(NodesFactory::createInstance(),
                    [
                        new ErrorHandlingMiddleware($this)
                    ]),
                new Factory\Function_(),
                new Factory\Interface_(),
                new Factory\Method(),
                new Factory\Property(new PrettyPrinter()),
                new Factory\Trait_(),
            ]);
            $this->project = $projectFactory->create('MyProject', $interfaceList);
            $this->log('Successfully parsed files.');
            $reflections = $this->getReflections();
            // $file = new LocalFile('test.php');

            var_dump( $this->extensions);*/
            }
        }
        return $response;

    }

    public function generate() {

        $data = [
            'title' => 'API Reference Test Page',
            'description' => 'This is a test page!',
            'content' => [
                [
                    'title' => 'Empty Test Section',
                    'description' => 'This is a test section!',
                ],
                [
                    'title' => 'Reference Test Section',
                    'description' => 'This is a test section!',
                    'content' => [
                        [
                            'title' => 'Empty Endpoint',
                            'description' => 'This is a test endpoint!',
                            'method' => 'GET',
                            'uri' => '/api/am/empty',
                            'parameters' => []
                        ],
                        [
                            'title' => 'Test Endpoint',
                            'description' => 'This is a test endpoint!',
                            'method' => 'POST',
                            'uri' => '/api/test/{user_id}/save',
                            'example_response' => '{ "hello" : "world" }',
                            'parameters' => [
                                [
                                    'resource' => 'param',
                                    'name' => 'user_id',
                                    'description' => 'This is testing uri params.',
                                ],
                                [
                                    'resource' => 'query',
                                    'type' => 'bool',
                                    'name' => 'is_new',
                                    'description' => 'This is testing get params.',
                                ],
                                [
                                    'resource' => 'form',
                                    'type' => 'string',
                                    'name' => 'name',
                                    'description' => 'This is testing form params.',
                                ],
                                [
                                    'resource' => 'form',
                                    'type' => 'string',
                                    'name' => 'name',
                                    'description' => 'This is testing form params.',
                                    'optional' => true,
                                ],
                                [
                                    'resource' => 'reqheader',
                                    'name' => 'Authorization',
                                    'description' => 'This is testing request headers param.'
                                ],
                                [
                                    'resource' => 'reqheader',
                                    'name' => 'Content-Type',
                                    'description' => 'This is testing request headers param.'
                                ],
                                [
                                    'resource' => 'resheader',
                                    'name' => 'Content-Type',
                                    'description' => 'This is testing response headers param.'
                                ],
                            ]],
                ]
                ]
            ]
        ];





        /*$response = $this->builder->build($data);
        $doc_file = fopen(base_path()."/docs/api.rst", "w") or die("Unable to open file!");
        fwrite($doc_file, $response);
        fclose($doc_file);*/


        $response = $this->fetchRoutes();

        return $response;
    }

    public static function getRouteClassAndMethod($action)
    {
        if ($action['uses'] !== null ) {
            if (is_array($action['uses'])) {
                return ;
            } elseif (is_string($action['uses'])) {
                return explode('@', $action['uses']);
            }
        }
        if (array_key_exists(0, $action) && array_key_exists(1, $action)) {
            return [
                0 => $action[0],
                1 => $action[1],
            ];
        }
    }
    private function getMappedRoutes() {
        $routes = [
            'api' => [],
            'web' => []
        ];
        foreach(Route::getRoutes() as $route) {
            $action = $route->getAction();
            $group =  strpos($route->uri, 'api') !== false || (array_key_exists('middleware', $action) && in_array('api', $action['middleware'])) ? 'api' : (strpos($route->uri, 'api') || (array_key_exists('middleware', $action) && in_array('web', $action['middleware'])) ? 'web' : null);
            if($route->getActionName() !== 'Closure' && !is_null($group) && $action['prefix']!=='sanctum' && $action['prefix']!=='_ignition' && array_key_exists('controller', $action)) {
                $actionData = $this->getRouteClassAndMethod($action);
                $routes[$group][$route->uri] = [
                    'name' => $route->getName(),
                    'class' => $actionData[0],
                    'method' => $actionData[1],
                    'methods' => $route->methods(),
                    'uri' => $route->uri,
                    'route' => is_null($route->getName()) ? $route : Route::getRoutes()->getByName($route->getName()),
                ];
            }

        }
        return $routes;
    }
    private function getStrategy(string $stage, array $context, array $arguments) {
        //$controller, $method, $route, $routeRules, $parsedRoute
        //ReflectionClass $controller, ReflectionMethod $method, Route $route, array $rulesToApply, array $context = []
        $strategies = $this->config->get("strategies.$stage",$this->strategies[$stage]);
        $context[$stage] = $context[$stage] ?? [];
        foreach ($strategies as $strategyClass) {
            $strategy = new $strategyClass($stage, $this->config);
            $strategyArgs = $arguments;
            $strategyArgs[] = $context;

            $results = $strategy(...$strategyArgs);
            //var_dump($results);
            if (! is_null($results)) {
                foreach ($results as $index => $item) {
                    if ($stage == 'responses') {
                        // Responses are additive
                        $context[$stage][] = $item;
                        continue;
                    }
                    // Using a for loop rather than array_merge or +=
                    // so it does not renumber numeric keys
                    // and also allows values to be overwritten

                    // Don't allow overwriting if an empty value is trying to replace a set one
                    if (! in_array($context[$stage], [null, ''], true) && in_array($item, [null, ''], true)) {
                        continue;
                    } else {
                        $context[$stage][$index] = $item;
                    }
                }
            }
        }
        return $context[$stage];

    }
    private function getParameters($method) {
        // https://www.php.net/manual/en/reflectionparameter.getattributes.php
        $parameters = [];
        foreach($method->getParameters() as $n => $parameter) {
            $paramData = [
                'name' => $parameter->name,
                'type' => !is_null($parameter->getClass()) ? $parameter->getClass()->name : $parameter->getType(),
                'optional' => $parameter->isOptional(),
                'attributes' => $parameter->getAttributes(),
                'position' => $parameter->getPosition(),
                'class' => $parameter->getDeclaringClass()->name,
                'function' => $parameter->getDeclaringFunction()->name,
            ];
            if($parameter->isDefaultValueAvailable()) {
                $paramData['default'] = $parameter->getDefaultValue();
                $paramData['default_constant'] = $parameter->getDefaultValueConstantName();
            }
            $parameters[] = $paramData;
        }
        return $parameters;
    }
    private function getUrlParameters($uri) {
        preg_match_all("/{[^}]*}/", $uri, $parameters);
        return $parameters;
    }
    private function format_route($context) {
        $name = is_null($context["name"])||$context["name"]==="" ? $context["uri"] : $context["name"];
        $name_header=implode('', array_map(function($letter) { return '+'; }, str_split($context["name"])));

        return <<<EOD
{$context["name"]}
                    {$name_header}

                    .. http:get::  {$context["uri"]}

                        Eros donec ac odio tempor. Arcu dui vivamus arcu felis bibendum ut tristique et egestas.

                        **Example response**:

                        .. sourcecode:: js

                            {
                                "id": 2,
                                "name": "Ervin Howell",
                                "username": "Antonette",
                                "email": "Shanna@melissa.tv",
                                "phone": "555-692-6593",
                                "website": "anastasia.net",
                                "company": {
                                    "name": "Deckow-Crist",
                                }
                            }

                        :query string ultrices: Blandit volutpat maecenas volutpat blandit aliquam.
                        :query int neque: Mattis molestie a iaculis at erat pellentesque adipiscing commodo elit.
EOD;
    }
    private function format_section($title, $description, $content) {
        $header = implode('', array_map(function($letter) { return '-'; }, str_split($title)));;
        return <<<EOD

                    {$title}
                    {$header}
                    
                    {$content}

EOD;
    }
    public function generateOld() {
        $response = [];
        $data = [];
        $page = [];
        foreach($this->getMappedRoutes() as $group => $routes) {
            if($group==='api') {
                foreach($routes as $route) {
                    $controller = new ReflectionClass($route['class']);
                    $method = $controller->getMethod($route['method']);
                    $context = [
                        'name' => $route['name'],
                        'controller' => $controller->name,
                        'method' => $method->name,
                        'id' => md5($route['uri'] . ':' . implode($route['methods'])),
                        'methods' => $route['methods'],
                        'parameters' => $this->getParameters($method),
                        'url_parameters' => $this->getUrlParameters($route['uri']),
                        'uri' => $route['uri']
                    ];
                    $context['metadata'] = $this->getStrategy('metadata', $context,  [$route['route'],$controller, $method, []]);
                    $groupName = array_key_exists('groupName', $context['metadata']) && $context['metadata']['groupName']!="" && !is_null($context['metadata']['groupName']) ? $context['metadata']['groupName'] : $group;
                    $groupDescription = array_key_exists('groupDescription', $context['metadata']) && $context['metadata']['groupDescription']!="" && !is_null($context['metadata']['groupDescription']) ? $context['metadata']['groupDescription'] : '';

                    if(!array_key_exists($groupName,$data)) {
                        $data[$groupName] = [
                            'name'=>$groupName,
                            'description'=>$groupDescription,
                            'routes'=>[]];
                    }
            }
                //$instance = app($routes[$group][$route->uri]['class']);


                //foreach($this->strategies as $stage => $strategyClass) {
                //    $context[$stage] = ;
                //}


                $data[$context['metadata']['groupName']]['routes'][] = $this->format_route($context);
                var_dump($data[$context['metadata']['groupName']]['routes']);

            }
        }

        $sections = [];
        foreach($data as $name => $group) {
            $sections[] = $this->format_section($name, $group['description'], implode('',$group['routes']));
        }
        $data = implode('\r',$sections);
        $response = <<<EOD
                    API Reference
                    =============
                    
                    Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.
                    
                    Fermentum Odio
                    --------------
                    
                    {$data}
                    
                    

EOD;
       // return app()->getRoutes();
        /*$routes = $routeMatcher->getRoutes($this->docConfig->get('routes'), $this->docConfig->get('router'));

        $generator = new Generator($this->docConfig);
        $parsedRoutes = $this->processRoutes($generator, $routes);

        $groupedRoutes = collect($parsedRoutes)
            ->groupBy('metadata.groupName')
            ->sortBy(static function ($group) {

                return $group->first()['metadata']['groupName'];
            }, SORT_NATURAL);
        $writer = new Writer(
            $this,
            $this->docConfig,
            $this->option('force')
        );
        $writer->writeDocs($groupedRoutes);*/
        //var_dump($routes);
       /* foreach ($routes as $route) {
            //var_dump($route->action);
            if(array_key_exists('middleware', $route->action)) {
                $group = (in_array('api', $route->action['middleware'])) ? 'api' : (in_array('web', $route->action['middleware']) ? 'web' : null);
                if(!is_null($group)) {
                    $data[$group][$route->uri] = $route;
                }
            }

            $response[] = $route;
            //var_dump($route->getAction());
        }*/

        $myfile = fopen("/var/www/test.rst", "w") or die("Unable to open file!");
        fwrite($myfile, $response);
        fclose($myfile);

        return $response;
    }



    protected function resolveClassMethodDependencies(array $parameters, $instance, $method)
    {
        if (!method_exists($instance, $method)) {
            return $parameters;
        }

        return $this->getDependencies(
            new ReflectionMethod($instance, $method)
        );
    }

    public function getDependencies(ReflectionFunctionAbstract $reflector)
    {
        return array_map(function ($parameter) {
            return $this->transformDependency($parameter);
        }, $reflector->getParameters());
    }

    protected function transformDependency(ReflectionParameter $parameter)
    {
        $class = $parameter->getClass();

        if (empty($class)) {
            return null;
        }

        return $class->name;
    }




    protected function getClassAnnotations($class): array
    {
        $reflection = new ReflectionClass($class);

        $annotations = $reflection->getDocComment();

        $blocks = explode("\n", $annotations);

        $result = [];

        foreach ($blocks as $block) {
            if (Str::contains($block, '@')) {
                $index = strpos($block, '@');
                $block = substr($block, $index);
                $exploded = explode(' ', $block);

                $paramName = str_replace('@', '', array_shift($exploded));
                $paramValue = implode(' ', $exploded);

                if (in_array($paramName, $this->booleanAnnotations)) {
                    $paramValue = true;
                }

                $result[$paramName] = $paramValue;
            }
        }

        return $result;
    }

    protected function getPathParams(): array
    {
        $params = [];

        preg_match_all('/{.*?}/', $this->uri, $params);

        $params = Arr::collapse($params);

        $result = [];

        foreach ($params as $param) {
            $key = preg_replace('/[{}]/', '', $param);

            $result[] = [
                'in' => 'path',
                'name' => $key,
                'description' => '',
                'required' => true,
                'type' => 'string'
            ];
        }

        return $result;
    }
    protected function parseResponse($response)
    {
        $produceList = $this->data['paths'][$this->uri][$this->method]['produces'];

        $produce = $response->headers->get('Content-type');

        if (is_null($produce)) {
            $produce = 'text/plain';
        }

        if (!in_array($produce, $produceList)) {
            $this->item['produces'][] = $produce;
        }

        $responses = $this->item['responses'];

        $responseExampleLimitCount = config('auto-doc.response_example_limit_count');

        $content = json_decode($response->getContent(), true);

        if (!empty($responseExampleLimitCount)) {
            if (!empty($content['data'])) {
                $limitedResponseData = array_slice($content['data'], 0, $responseExampleLimitCount, true);
                $content['data'] = $limitedResponseData;
                $content['to'] = count($limitedResponseData);
                $content['total'] = count($limitedResponseData);
            }
        }

        if (!empty($content['exception'])) {
            $uselessKeys = array_keys(Arr::except($content, ['message']));

            $content = Arr::except($content, $uselessKeys);
        }

        $code = $response->getStatusCode();

        if (!in_array($code, $responses)) {
            $this->saveExample(
                $code,
                json_encode($content, JSON_PRETTY_PRINT),
                $produce
            );
        }

        $action = Str::ucfirst($this->getActionName($this->uri));
        $definition = "{$this->method}{$action}{$code}ResponseObject";

        $this->saveResponseSchema($content, $definition);

        if (is_array($this->item['responses'][$code])) {
            $this->item['responses'][$code]['schema']['$ref'] = "#/definitions/{$definition}";
        }
    }

    protected function getParameterType(array $validation): string
    {
        $validationRules = $this->ruleToTypeMap;
        $validationRules['email'] = 'string';

        $parameterType = 'string';

        foreach ($validation as $item) {
            if (in_array($item, array_keys($validationRules))) {
                return $validationRules[$item];
            }
        }

        return $parameterType;
    }
    protected function parseRequestName($request)
    {
        $explodedRequest = explode('\\', $request);
        $requestName = array_pop($explodedRequest);
        $summaryName = str_replace('Request', '', $requestName);

        $underscoreRequestName = $this->camelCaseToUnderScore($summaryName);

        return preg_replace('/[_]/', ' ', $underscoreRequestName);
    }

    protected function getResponseDescription($code)
    {
        $defaultDescription = Response::$statusTexts[$code];

        $request = $this->getConcreteRequest();

        if (empty($request)) {
            return $defaultDescription;
        }

        $annotations = $this->getClassAnnotations($request);

        $localDescription = Arr::get($annotations, "_{$code}");

        if (!empty($localDescription)) {
            return $localDescription;
        }

        return Arr::get($this->config, "defaults.code-descriptions.{$code}", $defaultDescription);
    }

    protected function getActionName($uri): string
    {
        $action = preg_replace('[\/]', '', $uri);

        return Str::camel($action);
    }

}