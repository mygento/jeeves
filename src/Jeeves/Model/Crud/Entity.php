<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Interfaces;
use Mygento\Jeeves\Generators\Crud\Models;
use Mygento\Jeeves\Generators\Crud\Repositories;
use Mygento\Jeeves\Model\Generator;

class Entity extends Generator
{
    private $typehint;

    private $version;

    private $config = [];

    private $name;

    private $vendor;

    private $module;

    private $api;

    private $gui;

    private $readonly;

    private $withStore;

    private $tablename;

    private $cacheTag = null;

    private $primaryKey;

    public function __construct()
    {
    }

    public function generate()
    {
        if (empty($this->config)) {
            return;
        }
        $this->generateInterfaces();
        $this->generateModels();
        $this->generateRepository();
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
            $this->getConverter()->camelCaseToSnakeCase($this->vendor)
                . '_' . $this->getModuleLowercase()
                . '_' . $this->getEntityLowercase();
        $cacheable = $config['cacheable'] ?? false;

        $this->primaryKey = 'id';

        if ($cacheable) {
            $this->cacheTag = $config['cache_tag'] ?? $this->generateCacheTag();
        }

        if (!isset($config['route'])) {
            $config['route'] = [];
        }

        if (!isset($config['route']['admin']) || !$config['route']['admin']) {
            // $config['route']['admin'] = $this->getModuleLowercase();
        }

        //$routepath = $config['route']['admin'];
        $fields = $config['columns'] ?? [];
        if (empty($fields)) {
            return;
        }
    }

    public function setName(string $entityName)
    {
        $this->name = $entityName;
    }

    public function setModule(string $module)
    {
        $this->module = $module;
    }

    public function setVendor(string $vendor)
    {
        $this->vendor = $vendor;
    }

    private function generateModels()
    {
        $this->genModel();
        $this->genResourceModel();
        $this->genResourceCollection();
    }

    private function generateCacheTag(): string
    {
        return ''; //strtolower(substr($module, 0, 3) . '_' . substr($entity, 0, 1));
    }

    private function getNamespace(): string
    {
        return ucfirst($this->vendor) . '\\' . ucfirst($this->module);
    }

    private function getEventName($entity)
    {
        return implode('_', [
            $this->getVendorLowercase(),
            $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->module),
            $this->getConverter()->camelCaseToSnakeCase($entity),
        ]);
    }

    private function getModuleLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->module);
    }

    private function getVendorLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->vendor);
    }

    private function getEntityLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->name);
    }

    private function generateInterfaces()
    {
        $this->genModelInterface();
        $this->genModelRepositoryInterface();
        $this->genModelSearchInterface();
    }

    private function genModelInterface()
    {
        $generator = new Interfaces\Model();
        $filePath = $this->path . '/Api/Data/';
        $fileName = ucfirst($this->name) . 'Interface';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelInterface(
                $fileName,
                $this->getNamespace(),
                $this->cacheTag,
                $this->config['columns'],
                $this->withStore,
                $this->typehint
            )
        );
    }

    private function genModelRepositoryInterface()
    {
        $generator = new Interfaces\Repository();
        $filePath = $this->path . '/Api/';
        $fileName = ucfirst($this->name) . 'RepositoryInterface';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelRepositoryInterface(
                $this->name,
                $namePath . ucfirst($this->name) . 'Interface',
                $namePath . ucfirst($this->name) . 'SearchResultsInterface',
                $fileName,
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genModelSearchInterface()
    {
        $generator = new Interfaces();
        $filePath = $this->path . '/Api/Data/';
        $fileName = ucfirst($this->name) . 'SearchResultsInterface';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelSearchInterface(
                $this->name,
                $fileName,
                $namePath . ucfirst($this->name) . 'Interface',
                $this->getNamespace()
            )
        );
    }

    private function genModel()
    {
        $generator = new Models\Model();
        $filePath = $this->path . '/Model/';
        $fileName = ucfirst($this->name);
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModel(
                $fileName,
                $namePath . ucfirst($this->name) . 'Interface',
                'ResourceModel' . '\\' . ucfirst($this->name),
                $this->getNamespace(),
                $this->getEventName(ucfirst($this->name)),
                $this->cacheTag,
                $this->config['columns'],
                $this->withStore,
                $this->typehint
            )
        );
    }

    private function genResourceModel()
    {
        $generator = new Models\Resource();
        $filePath = $this->path . '/Model/ResourceModel/';
        $fileName = ucfirst($this->name);
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceModel(
                $fileName,
                $this->tablename,
                $this->primaryKey,
                $this->getNamespace(),
                ucfirst($this->name) . 'Interface',
                $this->withStore,
                $this->typehint
            )
        );
    }

    private function genResourceCollection()
    {
        $generator = new Models\Collection();
        $filePath = $this->path . '/Model/ResourceModel/' . ucfirst($this->name) . '/';
        $fileName = 'Collection';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceCollection(
                ucfirst($this->name),
                '\\' . $this->getNamespace() . '\\Model' . '\\' . ucfirst($this->name),
                '\\' . $this->getNamespace() . '\\Model\\ResourceModel' . '\\' . ucfirst($this->name),
                $this->getNamespace(),
                ucfirst($this->name) . 'Interface',
                $this->primaryKey,
                $this->withStore,
                $this->typehint
            )
        );
    }

    private function generateRepository()
    {
        $generator = new Repositories\Repository();
        $filePath = $this->path . '/Model/';
        $fileName = ucfirst($this->name) . 'Repository';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genRepository(
                $fileName,
                implode(' ', [
                    $this->getConverter()->getEntityName($this->module),
                    $this->getConverter()->getEntityName(ucfirst($this->name)),
                ]),
                $namePath . 'Api\\' . ucfirst($this->name) . 'RepositoryInterface',
                $namePath . 'Model\\ResourceModel\\' . ucfirst($this->name),
                $namePath . 'Model\\ResourceModel\\' . ucfirst($this->name) . '\\Collection',
                $namePath . 'Api\\Data\\' . ucfirst($this->name) . 'SearchResultsInterface',
                $namePath . 'Api\\Data\\' . ucfirst($this->name) . 'Interface',
                $this->getNamespace(),
                $this->withStore,
                $this->typehint
            )
        );
    }
}
