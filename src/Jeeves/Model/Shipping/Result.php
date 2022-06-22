<?php

namespace Mygento\Jeeves\Model\Shipping;

class Result
{
    private $path;
    private $module;
    private $carrierConfigs = [];
    private $defaultConfigs = [];

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

    public function getCarrierConfigs(): array
    {
        return $this->carrierConfigs;
    }

    public function updateCarrierConfigs(array $carrierConfigs)
    {
        $this->carrierConfigs = array_merge($this->carrierConfigs, $carrierConfigs);
    }

    public function getDefaultConfigs(): array
    {
        return $this->defaultConfigs;
    }

    public function updateDefaultConfigs(array $defaultConfigs)
    {
        $this->defaultConfigs = array_merge($this->defaultConfigs, $defaultConfigs);
    }
}
