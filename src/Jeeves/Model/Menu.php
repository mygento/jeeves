<?php

namespace Mygento\Jeeves\Model;

class Menu
{
    private $id;
    private $name;
    private $code;
    private $parent;
    private $resource;
    private $action;

    public function __construct(string $id, string $name, string $code, string $parent, string $resource, string $action = null)
    {
        $this->id = $id;
        $this->name = $name;
        $this->code = $code;
        $this->parent = $parent;
        $this->resource = $resource;
        $this->action = $action;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getCode(): string
    {
        return $this->code;
    }

    public function getParent(): string
    {
        return $this->parent;
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getAction(): ?string
    {
        return $this->action;
    }
}
