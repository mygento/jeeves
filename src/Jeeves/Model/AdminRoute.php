<?php

namespace Mygento\Jeeves\Model;

class AdminRoute
{
    private $id;
    private $name;
    private $path;

    public function __construct(string $id, string $name, string $path)
    {
        $this->id = $id;
        $this->name = $name;
        $this->path = $path;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getPath(): string
    {
        return $this->path;
    }
}
