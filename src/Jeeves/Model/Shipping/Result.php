<?php

namespace Mygento\Jeeves\Model\Shipping;

class Result
{
    private $path;

    private $module;

    public function getPath(): string
    {
        return $this->path;
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function setModule(string $module)
    {
        $this->module = $module;
    }
}
