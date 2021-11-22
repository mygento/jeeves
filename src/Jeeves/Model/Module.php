<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\Generators\Crud\Common;

class Module
{
    private $vendor;

    private $module;

    private $converter;

    public function __construct(string $vendor, string $module)
    {
        $this->vendor = $vendor;
        $this->module = $module;
    }

    public function setModule(string $module)
    {
        $this->module = $module;
    }

    public function setVendor(string $vendor)
    {
        $this->vendor = $vendor;
    }

    public function getNamespace(): string
    {
        return ucfirst($this->vendor) . '\\' . ucfirst($this->module);
    }

    public function getModule(): string
    {
        return $this->module;
    }

    public function getVendor(): string
    {
        return $this->vendor;
    }

    public function getFullname()
    {
        return ucfirst($this->vendor) . '_' . ucfirst($this->module);
    }

    public function getPrintName()
    {
        return $this->getConverter()->splitAtUpperCase($this->getModule());
    }

    private function getConverter(): Common
    {
        if (null === $this->converter) {
            $this->converter = new Common();
        }

        return $this->converter;
    }
}
