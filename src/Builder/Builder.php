<?php

namespace SeacoastBank\AutoDocumentation\Builder;

class Builder {

    private $type;
    private $defaults;

    function __construct($type = 'api') {
        $this->type = $type;
        $this->defaults = [
            'parameter' =>  ['name'=>'param','type'=>'string','variableName'=>null,'description'=>null,'optional'=>false],
            'endpoint' => ['title'=>null, 'description'=>null, 'method'=>'GET', 'uri'=>'/', 'example_response'=>'{}', 'parameters'=>[]],
            'section' => ['title'=>null, 'description'=>null, 'content'=>[]],
            'page' =>  ['title'=>'API Reference', 'description'=>null, 'content'=>[]]
        ];
    }

    private function setDefaults($data, $key) {
        foreach($this->defaults[$key] as $k => $v) {
            if(!array_key_exists($k, $data) || !isset($data[$k])) {
                $data[$k] = $v;
            }
        }
        return $data;
    }

    public function parameter($meta=[]) {
        /*
         * Resource Types
         * param (parameter, arg, argument) - url param
         * query (queryparameter, queryparam, qparam) - get param?
         * form (formparameter, formparam, fparam) - content body of form data (application/x-www-form-urlencoded or multipart/form-data)
         * json (jsonparameter, jsonparam) - content body of application/json
         * reqjsonobj, reqjson - SINGLE json field
         * resjsonobj, resjson - returned json single field
         * reqjsonarr, <jsonarr resjsonarr, >jsonarr
         * requestheader, reqheader, >header - request header field
         * responseheader, resheader, <header - response header field
         * statuscode, status, code
         */
        $meta = $this->setDefaults($meta, 'parameter');
        $optional = $meta['optional'] ? 'optional ' : '';
        return ":{$meta['name']} {$meta['type']} {$meta['variableName']}: {$optional}{$meta['description']} \r";
    }

    public function endpoint($meta=[]) {
        $meta = $this->setDefaults($meta, 'endpoint');
        $meta['header'] = $meta['title']!=='' ? implode('', array_map(function($l) { return '+'; }, str_split($meta['title']))) : '';
        $meta['parameters'] = implode("\r    ", array_map(function($d) { return $this->parameter($d); }, $meta['parameters']));
        return <<<EOD

{$meta['title']}
{$meta['header']}

.. http:{$meta['method']}::  {$meta['uri']}

    {$meta['description']}

    **Example response**:

    .. sourcecode:: js

        {$meta['example_response']}

    {$meta['parameters']}

EOD;
    }
    public function section($meta=[]) {
        $meta = $this->setDefaults($meta, 'section');
        $meta['header'] = $meta['title']!=='' ? implode('', array_map(function($l) { return '-'; }, str_split($meta['title']))) : '';
        $meta['data'] = implode("\r", array_map(function($d) { return $this->endpoint($d); }, $meta['data']));
        return <<<EOD

{$meta['title']}
{$meta['header']}

{$meta['description']}

{$meta['data']}

EOD;
    }
    public function page($meta=[]) {
        $meta = $this->setDefaults($meta, 'page');
        $meta['header'] = implode('', array_map(function($l) { return '='; }, str_split($meta['title'])));
        $meta['data'] = implode("\r", array_map(function($d) { return $this->section($d); }, $meta['data']));

        return <<<EOD
{$meta['title']}
{$meta['header']}

{$meta['description']}

{$meta['data']}
EOD;
    }

    public function build(&$meta) {
        return $this->page($meta);
    }

}