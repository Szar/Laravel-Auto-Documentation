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


class Parser {
    function __construct() {
        $this->lexer = new Lexer();
        $this->constExprParser = new ConstExprParser();
        $this->typeParser = new TypeParser($this->constExprParser);
        $this->parser = new PhpDocParser($this->typeParser, $this->constExprParser);
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
    public function parse($method) {
        $tokens = new TokenIterator($this->lexer->tokenize($method->getDocComment()));

        $node = $this->parser->parse($tokens);

        //echo "<br><br><br>";
       // var_dump($node);
        //echo "<br><br><br>";
        $comments = [
            "text" => [],
            "tags" => [],
            //"tag_values" => $node->text
        ];
       /* $comments = [
            "node" => $node,
            "tags" => $node->getTags(),
            "paramTags" => $node->getParamTagValues(),
            "varTags" => $node->getVarTagValues(),
            "varTags" => $node->getTagsByName('@urlParam'),
            //"name" => $route->method->getName()
        ];*/
        foreach($node->children as $child) {
            if($child->__toString()!=="" && !str_contains($child->__toString(),'@')) {
                $comments["text"][] = $child->__toString();
            }
        }
        foreach($node->getTags() as $tag) {
            if(array_key_exists(str_replace('@','',$tag->name), $this->types)) {
                /*$comments["tags"][] = [
                    "resource" => $tag["name"],
                    "resource" => $tag["name"],
                    "resource" => $tag["name"],
                    "resource" => $tag["name"],

                ];*/
                //$this->parseParamTagValue($this->lexer->tokenize($method->getDocComment($tag->text)));
                if(property_exists($tag->value, "value")) {
                    $this->parseParamTagValue(
                        new TokenIterator($this->lexer->tokenize($tag->value->value))
                        //$this->lexer->tokenize($tag->value->value)
                    );
                }

                $comments["tags"][] = $tag;
            }
        }
        return $comments;
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