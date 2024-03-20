<?php

namespace SeacoastBank\AutoDocumentation\Lib;

class DocumentationConfig
{
    private $data;

    public function __construct(array $config = [])
    {
        $this->data = $config;
    }

    public function get($key, $default = null)
    {
        return data_get($this->data, $key, $default);
    }
}
