<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\Generators\Crud\Common;

class Module
{
    private $vendor;
    private $module;
    private $route = null;
    private $converter;
    private $typehint;

    public function __construct(string $vendor, string $module, bool $typehint = true)
    {
        $this->vendor = $vendor;
        $this->module = $module;
        $this->typehint = $typehint;
    }

    public function setConfig(array $config)
    {
        $this->route = $config['admin_route'] ?? $this->getModuleLowercase();
        $this->typehint = $config['typehint'] ?? $this->typehint;
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

    public function getFullname(): string
    {
        return ucfirst($this->vendor) . '_' . ucfirst($this->module);
    }

    public function getRouteName(): string
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->module);
    }

    public function getModuleLowercase(): string
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->module);
    }

    public function getEventPrefix(): string
    {
        return
            $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->vendor)
            . '_'
            . $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->module);
    }

    public function getVendorLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->vendor);
    }

    public function getAdminRoute(): string
    {
        if ($this->route === null) {
            $this->route = $this->getModuleLowercase();
        }

        return $this->route;
    }

    public function getFullPrintName(): string
    {
        return $this->getConverter()->splitAtUpperCase($this->getVendor())
            . ' '
            . $this->getConverter()->splitAtUpperCase($this->getModule());
    }

    public function getPrintName(): string
    {
        return $this->getConverter()->splitAtUpperCase($this->getModule());
    }

    public function hasTypehint(): bool
    {
        return $this->typehint;
    }

    private function getConverter(): Common
    {
        if (null === $this->converter) {
            $this->converter = new Common();
        }

        return $this->converter;
    }
}
