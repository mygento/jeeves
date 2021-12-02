<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Model\Generator;
use Mygento\Jeeves\Model\Module;

class Entity extends Generator
{
    private const DEFAULT_KEY = 'id';

    private $typehint = false;

    private $version;

    private $config = [];

    private $name;

    /** @var Module */
    private $module;

    private $api;

    private $gui;

    private $readonly = false;

    private $withStore = false;

    private $tablename;

    private $cacheTag = null;

    private $primaryKey;

    private $adminRoute;

    public function __construct()
    {
    }

    public function getConfig()
    {
        return $this->config;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function getPrimaryKey(): string
    {
        return $this->primaryKey;
    }

    public function getCacheTag(): ?string
    {
        return $this->cacheTag;
    }

    public function getColumns(): array
    {
        return $this->config['columns'];
    }

    public function getIndexes(): array
    {
        return $this->config['indexes'] ?? [];
    }

    public function getFk(): array
    {
        return $this->config['fk'] ?? [];
    }

    public function withStore(): bool
    {
        return $this->withStore;
    }

    public function hasTypehint(): bool
    {
        return $this->typehint;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setTypeHint(bool $hint)
    {
        $this->typehint = $hint;
    }

    public function setVersion(string $version)
    {
        $this->version = $version;
    }

    public function setConfig(array $config)
    {
        $this->config = $config;
        $this->typehint = $config['settings']['typehint'] ?? $this->typehint;
        $this->version = $config['settings']['version'] ?? $this->version;

        $this->api = $config['api'] ?? false;
        $this->gui = $config['gui'] ?? true;

        $this->readonly = $config['readonly'] ?? false;
        $this->withStore = $config['per_store'] ?? false;

        $this->tablename = $config['tablename'] ??
            $this->getConverter()->camelCaseToSnakeCase($this->module->getVendor())
                . '_' . $this->getModule()->getModuleLowercase()
                . '_' . $this->getEntityLowercase();
        $cacheable = $config['cacheable'] ?? false;

        $this->primaryKey = self::DEFAULT_KEY;

        if ($cacheable) {
            $this->cacheTag = $config['cache_tag'] ?? $this->generateCacheTag();
        }

        if (!isset($config['route'])) {
            $config['route'] = [];
        }

        $this->adminRoute = $this->module->getAdminRoute();

        $fields = $config['columns'] ?? [];
        if (empty($fields)) {
            return;
        }

        foreach ($fields as $name => $value) {
            if (isset($value['pk']) && $value['pk'] === true) {
                $this->primaryKey = $name;
            }
        }
    }

    public function setName(string $entityName)
    {
        $this->name = $entityName;
    }

    public function getNamespace(): string
    {
        return $this->getModule()->getNamespace();
    }

    public function getTablename(): string
    {
        return $this->tablename;
    }

    public function setModule(Module $module)
    {
        $this->module = $module;
    }

    public function getModule(): Module
    {
        return $this->module;
    }

    public function hasGui(): bool
    {
        return $this->gui;
    }

    public function getEntityAcl()
    {
        return $this->getFullname() . '::' . $this->getEntityLowercase();
    }

    public function getEventName(string $entity): string
    {
        return implode('_', [
            $this->module->getVendorLowercase(),
            $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->module->getModule()),
            $this->getConverter()->camelCaseToSnakeCase($entity),
        ]);
    }

    public function getEntityName(): string
    {
        return $this->getConverter()->getEntityName($this->name);
    }

    public function getEntityLowercase(): string
    {
        return $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->name);
    }

    public function getLowerCaseWithModule()
    {
        return $this->getModule()->getModuleLowercase()
            . '_'
            . str_replace(
                '_',
                '',
                $this->getConverter()->camelCaseToSnakeCase($this->name)
            );
    }

    public function isReadOnly(): bool
    {
        return $this->readonly;
    }

    public function getAdminRoute(): string
    {
        return $this->adminRoute;
    }

    public function getFullname(): string
    {
        return $this->module->getFullname();
    }

    public function getPrintName(): string
    {
        return $this->getConverter()->getEntityPrintName($this->name);
    }

    public function getEntityAclTitle()
    {
        return
            $this->getConverter()->splitAtUpperCase($this->module->getModule())
                . ' '
                . $this->getPrintName();
    }

    private function generateCacheTag(): string
    {
        return strtolower(substr($this->module->getModule(), 0, 3) . '_' . substr($this->name, 0, 1));
    }
}
