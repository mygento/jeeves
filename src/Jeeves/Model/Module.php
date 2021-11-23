<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\Generators\Crud\Common;

class Module
{
    private $vendor;

    private $module;

    private $route;

    private $converter;

    public function __construct(string $vendor, string $module)
    {
        $this->vendor = $vendor;
        $this->module = $module;
    }

    public function setConfig(array $config)
    {
        $this->route = $config['settings']['admin_route'] ?? $this->getModuleLowercase();
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

    public function getVendorLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->vendor);
    }

    public function getAdminRoute(): string
    {
        return $this->route;
    }

    public function getPrintName(): string
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
