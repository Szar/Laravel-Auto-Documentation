<?php

namespace SeacoastBank\AutoDocumentation\Parser;

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\ContextFactory;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;

class Parser {
    function __construct() {
        $this->http_domain_params = [
            "param" => "param",
            "urlParam" => "query",
            "getParam" => "query",
            "postParam" => "form",
            "bodyParam" => "form",
            "jsonParam" => "reqjson",
            "request" => "reqjson",
            "response" => "resjson",
            "return" => "resjson",
            "requestHeader" => "reqheader",
            "responseHeader" => "resheader",
            "status" => "status",
            "internal" => "internal",
            "throws" => "throws"
        ];
        $this->custom_params = ["query","form"];
    }
    private function comments($method) {
        $method->getDocComment();
    }
    private function getTagsByType($docBlock, $type) {
        $helper = new PhpDocTypeHelper();
        return array_map(function($param) use ($helper) {
            $methods = gettype($param)==="object" ? get_class_methods($param) : false;
            $name = in_array('getName', $methods) ? (array_key_exists($param->getName(), $this->http_domain_params) ?  $this->http_domain_params[$param->getName()] : $param->getName()) : null;
            $parts = explode(' ',$param->__toString());
            $type = in_array($name, $this->custom_params) ? $parts[0] : (in_array('getType', $methods) ? $param->getType()->__toString() : null);
            return [
                'name' => $name,
                'type' => explode('|',$type)[0],
                'description' => in_array($name, $this->custom_params) ? explode( $parts[1], $param->getDescription()->__toString())[1] : (in_array('getDescription', $methods) ? $param->getDescription()->__toString() : null),
                'variableName' => in_array($name, $this->custom_params) ? $parts[1] : (in_array('getVariableName', $methods) ? $param->getVariableName() : null),
                'text' => $param->__toString(),
                'render' => $param->render(),
            ];
        }, $docBlock->getTagsByName($type));
    }
    public function parse(&$class, &$method) {
        $docBlock = DocBlockFactory::createInstance()->create($method, (new ContextFactory())->createFromReflector($method));
        $data = [
            "summary" => $docBlock->getSummary(),
            "description" => $docBlock->getDescription()->__toString(),
            "parameters" => [],
        ];
        foreach($this->http_domain_params as $type => $name) {
            foreach($this->getTagsByType($docBlock, $type) as $param) {
                $data["parameters"][] = $param;
            }
        }
        return $data;
    }

}