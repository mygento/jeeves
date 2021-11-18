<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Generators\Crud\Controllers;
use Mygento\Jeeves\Generators\Crud\Interfaces;
use Mygento\Jeeves\Generators\Crud\Layouts;
use Mygento\Jeeves\Generators\Crud\Models;
use Mygento\Jeeves\Generators\Crud\Repositories;
use Mygento\Jeeves\Generators\Crud\Ui;
use Mygento\Jeeves\Model\Generator;

class Entity extends Generator
{
    private const DEFAULT_KEY = 'id';

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
        $this->generateSearchResults();

        if ($this->withStore) {
            $this->genReadHandler();
            $this->genSaveHandler();
            $this->getRepoFilter();
        }

        if ($this->gui) {
            $this->generateControllers();
            $this->genAdminLayouts();

            $this->genAdminUI();
            $this->genGridCollection();
        }
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

        $this->primaryKey = self::DEFAULT_KEY;

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
        return strtolower(substr($this->module, 0, 3) . '_' . substr($this->name, 0, 1));
    }

    private function getNamespace(): string
    {
        return ucfirst($this->vendor) . '\\' . ucfirst($this->module);
    }

    private function getFullname()
    {
        return ucfirst($this->vendor) . '_' . ucfirst($this->module);
    }

    private function getEntityAcl()
    {
        return $this->getFullname() . '::' . $this->getEntityLowercase();
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
                $this->primaryKey,
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
        $generator = new Interfaces\Search();
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
                $this->getNamespace(),
                $this->typehint
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

    private function generateSearchResults()
    {
        $generator = new Repositories\Search();
        $filePath = $this->path . '/Model/';
        $fileName = ucfirst($this->name) . 'SearchResults';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelSearch(
                $fileName,
                $namePath . ucfirst($this->name) . 'SearchResultsInterface',
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genReadHandler()
    {
        $generator = new Models\Read();
        $filePath = $this->path . '/Model/ResourceModel/' . ucfirst($this->name) . '/Relation/Store/';
        $fileName = 'ReadHandler';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genReadHandler(
                ucfirst($this->name),
                $namePath . 'Model\\ResourceModel\\' . ucfirst($this->name),
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genSaveHandler()
    {
        $generator = new Models\Save();
        $filePath = $this->path . '/Model/ResourceModel/' . ucfirst($this->name) . '/Relation/Store/';
        $fileName = 'SaveHandler';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genSaveHandler(
                ucfirst($this->name),
                ucfirst($this->name) . 'Interface',
                $namePath . 'Model\\ResourceModel\\' . ucfirst($this->name),
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function getRepoFilter()
    {
        $generator = new Repositories\Filter();
        $filePath = $this->path . '/Model/SearchCriteria/';
        $fileName = ucfirst($this->name) . 'StoreFilter';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->getRepoFilter(
                $fileName,
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function generateControllers()
    {
        $this->genAdminAbstractController();
        $this->genAdminViewController();

        if (!$this->readonly) {
            $this->genAdminEditController();
            $this->genAdminSaveController();
            $this->genAdminDeleteController();
            $this->genAdminNewController();
//            $this->genAdminInlineController($controllerGenerator, ucfirst($entity));
//            $this->genAdminMassController($controllerGenerator, ucfirst($entity));
        }
    }

    private function genAdminAbstractController()
    {
        $generator = new Controllers\Shared();
        $filePath = $this->path . '/Controller/Adminhtml/';
        $fileName = ucfirst($this->name);
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminAbstractController(
                $fileName,
                $this->getEntityAcl(),
                $namePath . 'Api\\' . ucfirst($this->name) . 'RepositoryInterface',
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminViewController()
    {
        $generator = new Controllers\View();
        $filePath = $this->path . '/Controller/Adminhtml/' . ucfirst($this->name) . '/';
        $fileName = 'Index';
        $namePath = '\\' . $this->getNamespace() . '\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminViewController(
                ucfirst($this->name),
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase(),
                $namePath . 'Api\\' . ucfirst($this->name) . 'RepositoryInterface',
                $this->getEntityAcl(),
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminEditController()
    {
        $generator = new Controllers\Edit();
        $filePath = $this->path . '/Controller/Adminhtml/' . ucfirst($this->name) . '/';
        $fileName = 'Edit';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminEditController(
                ucfirst($this->name),
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase(),
                $namePath . 'Api\\' . ucfirst($this->name) . 'RepositoryInterface',
                $namePath . 'Api\\Data\\' . ucfirst($this->name) . 'Interface',
                $this->getEntityAcl(),
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminSaveController()
    {
        $generator = new Controllers\Save();
        $filePath = $this->path . '/Controller/Adminhtml/' . ucfirst($this->name) . '/';
        $fileName = 'Save';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminSaveController(
                ucfirst($this->name),
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase(),
                $namePath . 'Api\\' . ucfirst($this->name) . 'RepositoryInterface',
                $namePath . 'Api\\Data\\' . ucfirst($this->name) . 'Interface',
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminDeleteController()
    {
        $generator = new Controllers\Delete();
        $filePath = $this->path . '/Controller/Adminhtml/' . ucfirst($this->name) . '/';
        $fileName = 'Delete';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminDeleteController(
                ucfirst($this->name),
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminNewController()
    {
        $generator = new Controllers\Create();
        $filePath = $this->path . '/Controller/Adminhtml/' . ucfirst($this->name) . '/';
        $fileName = 'NewAction';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminNewController(
                ucfirst($this->name),
                $fileName,
                $namePath . 'Api\\' . ucfirst($this->name) . 'RepositoryInterface',
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminInlineController()
    {
        $generator = new Controllers\Inline();
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'InlineEdit';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminInlineController(
                $entityName,
                $fileName,
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminMassController()
    {
        $generator = new Controllers\Mass();
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'MassDelete';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminMassController(
                $entityName,
                $fileName,
                $namePath . 'Model\\ResourceModel\\' . $entityName . '\\CollectionFactory',
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminLayouts()
    {
        $parent = $this->getModuleLowercase() . '_' . $this->getEntityLowercase();
        $this->genAdminLayoutIndex($parent);

        if (!$this->readonly) {
            $editUiComponent = $parent . '_edit';
            $this->genAdminLayoutEdit($parent, $editUiComponent);
            $this->genAdminLayoutNew($parent, $editUiComponent);
        }
    }

    private function genAdminLayoutIndex(string $parent)
    {
        $generator = new Layouts\Index();

        $uiComponent = $parent . '_listing';
        $path = $parent . '_index';
        $this->writeFile(
            $this->path . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutIndex($uiComponent)
        );
    }

    private function genAdminLayoutEdit(string $parent, string $editUiComponent)
    {
        $generator = new Layouts\Edit();

        $path = $parent . '_edit';
        $this->writeFile(
            $this->path . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutEdit($editUiComponent)
        );
    }

    private function genAdminLayoutNew(string $parent, string $editUiComponent)
    {
        $generator = new Layouts\Create();
        $path = $parent . '_new';
        $this->writeFile(
            $this->path . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutNew($editUiComponent)
        );
    }

    private function genAdminUI()
    {
        $this->genAdminUiDataProvider();
        $this->genAdminUiListing();

        if (!$this->readonly) {
            $this->genAdminUIActions();
            $this->genAdminUiEdit();
        }
    }

    private function genAdminUIActions()
    {
        return;
        $filePath = $this->path . '/Ui/Component/Listing/';
        $fileName = ucfirst($entity) . 'Actions';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
                $generator->getActions(
                    $routepath,
                    $this->getEntityLowercase($entity),
                    $this->getNamespace(),
                    ucfirst($entity) . 'Actions'
                )
        );
    }

    private function genAdminUiDataProvider()
    {
        $generator = new Ui\DataProvider();
        $filePath = $this->path . '/Model/' . ucfirst($this->name) . '/';
        $fileName = 'DataProvider';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->getProvider(
                $this->name,
                '\\' . $this->getNamespace() . '\Model\\ResourceModel\\' . ucfirst($this->name) . '\\Collection',
                $this->getNamespace() . '\Model\\ResourceModel\\' . ucfirst($this->name) . '\\CollectionFactory',
                $fileName,
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase(),
                $this->getNamespace(),
                $this->typehint
            )
        );
    }

    private function genAdminUiListing()
    {
        return;
        $parent = $this->getModuleLowercase() . '_' . $this->getEntityLowercase($entity);

        $url = $this->getModuleLowercase() . '/' . $this->getEntityLowercase($entity);

        $uiComponent = $parent . '_listing';
        $common = $parent . '_listing' . '.' . $parent . '_listing.';
        $this->writeFile(
            $this->path . '/view/adminhtml/ui_component/' . $uiComponent . '.xml',
            $generator->generateAdminUiIndex(
                $uiComponent,
                $uiComponent . '_data_source',
                $parent . '_columns',
                'Add New ' . $this->getConverter()->getEntityName($entity),
                $this->getEntityAcl($entity),
                $this->getNamespace() . '\Ui\Component\Listing\\' . ucfirst($entity) . 'Actions',
                $url . '/inlineEdit',
                $url . '/massDelete',
                $common . $parent . '_columns.ids',
                $common . $parent . '_columns_editor',
                $fields,
                $this->readonly
            )
        );
    }

    private function genAdminUiEdit()
    {
        return;
        $uiComponent = $parent . '_edit';
        $dataSource = $uiComponent . '_data_source';
        $provider = $this->getNamespace() . '\Model\\' . ucfirst($entity) . '\DataProvider';
        $this->writeFile(
            $this->path . '/view/adminhtml/ui_component/' . $uiComponent . '.xml',
            $generator->generateAdminUiForm(
                $uiComponent,
                $dataSource,
                $url . '/save',
                $provider,
                $entity,
                $fields,
                $this->withStore
            )
        );
    }

    private function genGridCollection()
    {
        return;
        $filePath = $this->path . '/Model/ResourceModel/' . ucfirst($entity) . '/Grid/';
        $fileName = 'Collection';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->generateGridCollection(
                $entity,
                $this->getNamespace(),
                $fileName,
                $this->getNamespace() . '\Model\\ResourceModel\\' . ucfirst($entity) . '\\Collection',
                $this->withStore
            )
        );
    }
}
