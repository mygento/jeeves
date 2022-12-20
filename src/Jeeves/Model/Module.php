<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\Generators\Crud\Common;

class Module
{
    private string $vendor;
    private string $module;
    private ?string $route = null;
    private ?Common $converter = null;
    private string $phpVersion;

    public function __construct(string $vendor, string $module, string $phpVersion)
    {
        $this->vendor = $vendor;
        $this->module = $module;
        $this->phpVersion = $phpVersion;
    }

    public function setConfig(array $config)
    {
        $this->route = $config['admin_route'] ?? $this->getModuleLowercase();
        $this->phpVersion = $config['php_version'] ?? $this->phpVersion;
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

    public function getPhpVersion(): string
    {
        return $this->phpVersion;
    }

    private function getConverter(): Common
    {
        if (null === $this->converter) {
            $this->converter = new Common();
        }

        return $this->converter;
    }
}
