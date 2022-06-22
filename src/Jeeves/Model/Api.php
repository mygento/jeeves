<?php

namespace Mygento\Jeeves\Model;

class Api
{
    private $url;
    private $method;
    private $class;
    private $classMethod;
    private $resource;

    public function __construct(
        string $url,
        string $method,
        string $class,
        string $classMethod,
        string $resource
    ) {
        $this->url = $url;
        $this->method = $method;
        $this->class = $class;
        $this->classMethod = $classMethod;
        $this->resource = $resource;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getClass(): string
    {
        return $this->class;
    }

    public function getClassMethod(): string
    {
        return $this->classMethod;
    }

    public function getResource(): string
    {
        return $this->resource;
    }
}
