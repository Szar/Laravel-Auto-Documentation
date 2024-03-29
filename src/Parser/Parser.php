<?php

namespace SeacoastBank\AutoDocumentation\Parser;

use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\ContextFactory;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;

class Parser {
    function __construct() {
        /*$this->types = [
            "param" => "param",
            "urlParam" => "query",
            "bodyParam" => "form",
            "request" => "reqjson",
            "response" => "resjson",
            "requestHeader" => "reqheader",
            "responseHeader" => "resheader",
            "status" => "status",
        ];*/
        $this->types = ["uses","param","urlParam","getParam","postParam","bodyParam","jsonParam","return","internal","throws"];
    }
    private function comments($method) {
        $method->getDocComment();
    }
    private function getTagsByType($docBlock, $type) {
        $helper = new PhpDocTypeHelper();
        return array_map(function($param) use ($helper) {
            $methods = gettype($param)==="object" ? get_class_methods($param) : false;
            return [
                'name' => in_array('getName', $methods) ? $param->getName() : null,
                'type' => in_array('getType', $methods) ? $param->getType()->__toString() : null,
                'description' => in_array('getDescription', $methods) ? $param->getDescription()->__toString() : null,
                'variableName' => in_array('getVariableName', $methods) ? $param->getVariableName() : null,
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
        foreach($this->types as $type) {
            $data["parameters"][] = $this->getTagsByType($docBlock, $type);
        }
        return $data;
    }

}