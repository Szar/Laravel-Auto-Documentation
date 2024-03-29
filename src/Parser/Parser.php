<?php

namespace SeacoastBank\AutoDocumentation\Parser;

use PHPStan\PhpDocParser\Ast\NodeTraverser;
use PHPStan\PhpDocParser\Ast\NodeVisitor\CloningVisitor;
use PHPStan\PhpDocParser\Ast\PhpDoc\ParamTagValueNode;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocNode;
use PHPStan\PhpDocParser\Ast\Type\IdentifierTypeNode;
use PHPStan\PhpDocParser\Lexer\Lexer;
use PHPStan\PhpDocParser\Parser\ConstExprParser;
use PHPStan\PhpDocParser\Parser\PhpDocParser;
use PHPStan\PhpDocParser\Parser\TokenIterator;
use PHPStan\PhpDocParser\Parser\TypeParser;
use PHPStan\PhpDocParser\Ast\PhpDoc\PhpDocTextNode;
use Symfony\Component\PropertyInfo\PhpStan\NameScopeFactory;
use Symfony\Component\PropertyInfo\PropertyTypeExtractorInterface;
use Symfony\Component\PropertyInfo\Type;
//use Symfony\Component\PropertyInfo\Util\PhpStanTypeHelper;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\PhpStanExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
//use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
//use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tags\InvalidTag;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\DocBlockFactoryInterface;
use phpDocumentor\Reflection\Types\Context;
use phpDocumentor\Reflection\Types\ContextFactory;
use Symfony\Component\PropertyInfo\Util\PhpDocTypeHelper;

class Parser {
    //private $phpStanTypeHelper;
    //private $typeParser;
    //private $nameScopeFactory;
    //private nameScopeFactory;
    //private Lexer $lexer;

    function __construct() {
        $this->lexer = new Lexer();
        //$this->lexer->TOKEN_LABELS[] = XXX;
        $this->constExprParser = new ConstExprParser();
        $this->typeParser = new TypeParser($this->constExprParser);
        $this->parser = new PhpDocParser($this->typeParser, $this->constExprParser);
        //$this->phpDocExtractor = new PhpDocExtractor();

        //?DocBlockFactoryInterface $docBlockFactory = null,
       // ?array $mutatorPrefixes = null,
        //    ?array $accessorPrefixes = null,
        //        ?array $arrayMutatorPrefixes = null
        //$this->phpStanTypeHelper = new Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor\PhpStanTypeHelper();
        $this->phpDocExtractor = new PhpDocExtractor();
        $this->phpStanExtractor = new PhpStanExtractor();
        $this->reflectionExtractor = new ReflectionExtractor();
        $this->phpDocTypeHelper = new PhpDocTypeHelper();

        $this->contextFactory = new ContextFactory();
        $this->docBlockFactory = DocBlockFactory::createInstance();

        $listExtractors = [$this->reflectionExtractor];
        $typeExtractors = [$this->phpDocExtractor, $this->reflectionExtractor];
        $descriptionExtractors = [$this->phpDocExtractor];
        $accessExtractors = [$this->reflectionExtractor];
        $propertyInitializableExtractors = [$this->reflectionExtractor];


        $this->extractor = new PropertyInfoExtractor(
            $listExtractors,
            $typeExtractors,
            $descriptionExtractors,
            $accessExtractors,
            $propertyInitializableExtractors
        );

        $this->types = [
            "param" => "param",
            "urlParam" => "query",
            "bodyParam" => "form",
            "request" => "reqjson",
            "response" => "resjson",
            "requestHeader" => "reqheader",
            "responseHeader" => "resheader",
            "status" => "status",
        ];
    }

    private function comments($method) {
        $method->getDocComment();
    }
    private function getTagsByType($docBlock, $type) {
        $helper = $this->phpDocTypeHelper;
        return array_map(function($param) use ($helper) {
            $methods = gettype($param)==="object" ? get_class_methods($param) : [];
            //$variables = gettype($param)==="object" ? get_class_vars($param) : null;
            //var_dump(gettype($param));
            //$variables = get_class_vars($param);
            //echo "<br>";
            //print_r(get_class_methods($param));
            //echo "<br>";
            //print_r(get_class_vars($param));
            //echo "<br>";
           //echo "<br>";
            //bodyTemplate, param, tags, type
            return [
                'name' => in_array('getName', $methods) ? $param->getName() : null,
                'type' => in_array('getType', $methods) ? $param->getType()->__toString() : null,
                /*'types' => in_array('getType', $methods) ? array_map(function($t){
                    return [
                        'getClassName' => $t->getClassName(),
                        'isCollection' => $t->isCollection(),
                        'getCollectionKeyTypes'=>$t->getCollectionKeyTypes(),
                        'getCollectionKeyTypes'=>$t->getCollectionValueTypes()
                    ]; //get_class_methods($t);
                    }, $helper->getTypes($param->getType())) : null,
                //'tags' => $param->getTags(),*/
                'description' => in_array('getDescription', $methods) ? $param->getDescription()->__toString() : null,
                //'param' => $param->getParam(),
                //'bodyTemplate' => $param->getBodyTemplate(),
                'variableName' => in_array('getVariableName', $methods) ? $param->getVariableName() : null,
                'text' => $param->__toString(),
                'render' => $param->render(),
                //'class_methods' => $methods,
                //'class_methods' => $variables,

            ];
        }, $docBlock->getTagsByName($type));
    }
    public function parse(&$class, &$method) {
        //$tokens = new TokenIterator($this->lexer->tokenize($method->getDocComment()));

        //$node = $this->parser->parse($tokens);

        //echo "<br><br><br>";
       // var_dump($node);
        //echo "<br><br><br>";

       /* $comments = [
            "node" => $node,
            "tags" => $node->getTags(),
            "paramTags" => $node->getParamTagValues(),
            "varTags" => $node->getVarTagValues(),
            "varTags" => $node->getTagsByName('@urlParam'),
            //"name" => $route->method->getName()
        ];*/

        //$t = $this->phpDocExtractor->getDocBlock($method, 'g');
        /*
         * phpdoc
         * $this->phpDocExtractor->getTypes('Symfony\Component\PropertyInfo\Tests\Fixtures\InvalidDummy', 'foo')
         * $this->phpDocExtractor->getShortDescription('Symfony\Component\PropertyInfo\Tests\Fixtures\InvalidDummy', 'foo')
         * $this->phpDocExtractor->getLongDescription('Symfony\Component\PropertyInfo\Tests\Fixtures\InvalidDummy', 'foo')
         *
         * phpstan
         * $this->phpStanExtractor->getTypes(PhpStanOmittedParamTagTypeDocBlock::class, 'omittedType')
         */
        //$t = $this->phpDocExtractor->getTypes($class, $method);
        //echo "<br><br><br>phpDocExtractor<br><br>";
        //var_dump($class);
        $docBlock = $this->docBlockFactory->create($method, $this->contextFactory->createFromReflector($method));
        $data = [
            "summary" => $docBlock->getSummary(),
            "description" => $docBlock->getDescription()->__toString(),
            "tags" => [],
        ];

        $propertyTypes = ["uses","param","urlParam","bodyParam","return","internal","throws"];
        foreach($propertyTypes as $type) {
            $data["tags"][] = $this->getTagsByType($docBlock, $type);
        }

        return $data;
    }
    private function parseOptionalDescription($tokens, $limitStartToken = false)
    {
        if ($limitStartToken) {
            foreach (self::DISALLOWED_DESCRIPTION_START_TOKENS as $disallowedStartToken) {
                if (!$tokens->isCurrentTokenType($disallowedStartToken)) {
                    continue;
                }

                $tokens->consumeTokenType(Lexer::TOKEN_OTHER); // will throw exception
            }

            if (
                $this->requireWhitespaceBeforeDescription
                && !$tokens->isCurrentTokenType(Lexer::TOKEN_PHPDOC_EOL, Lexer::TOKEN_CLOSE_PHPDOC, Lexer::TOKEN_END)
                && !$tokens->isPrecededByHorizontalWhitespace()
            ) {
                $tokens->consumeTokenType(Lexer::TOKEN_HORIZONTAL_WS); // will throw exception
            }
        }

        return $this->parseText($tokens)->text;
    }
    private function parseRequiredVariableName($tokens)
    {
        $parameterName = $tokens->currentTokenValue();
        echo "<br><br>";
        var_dump($tokens);
        echo "<br><br>";
        var_dump(Lexer::TOKEN_VARIABLE);
        echo "<br><br>";
        $tokens->consumeTokenType(Lexer::TOKEN_OTHER);

        return $parameterName;
    }
    private function parseParamTagValue($tokens) {
        if (
        $tokens->isCurrentTokenType(Lexer::TOKEN_REFERENCE, Lexer::TOKEN_VARIADIC, Lexer::TOKEN_VARIABLE)
        ) {
            $type = null;
        } else {
            $type = $this->typeParser->parse($tokens);
        }

        $isReference = $tokens->tryConsumeTokenType(Lexer::TOKEN_REFERENCE);
        $isVariadic = $tokens->tryConsumeTokenType(Lexer::TOKEN_VARIADIC);
        $parameterName = $this->parseRequiredVariableName($tokens);
        $description = $this->parseOptionalDescription($tokens);

        if ($type !== null) {
            return new Ast\PhpDoc\ParamTagValueNode($type, $isVariadic, $parameterName, $description, $isReference);
        }

        return new Ast\PhpDoc\TypelessParamTagValueNode($isVariadic, $parameterName, $description, $isReference);
    }

}