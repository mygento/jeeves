<?php

namespace Mygento\Jeeves\Console\Command;

use Mygento\Jeeves\Console\Application;
use Mygento\Jeeves\Model\Crud;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModelCrud extends BaseCommand
{
    private const NEW_CONFIG = ['entities', 'shipping', 'settings'];

    private $vendor;

    private $module;

    private $path;

    private $api = false;

    private $gui = true;

    private $readonly = false;

    private $withStore = false;

    private $menu = [];

    private $acl = [];

    private $events = [];

    private $admin;

    private $guiList = [];

    private $di = [];

    private $db = [];

    protected function configure()
    {
        $this
            ->setName('generate-model-crud')
            ->setAliases(['generate_model_crud', 'crud'])
            ->setDescription('Generate Model Crud')
            ->setDefinition([
                new InputArgument('module', InputArgument::OPTIONAL, 'Name of the module'),
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the entity'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
                new InputOption('tablename', null, InputOption::VALUE_OPTIONAL, 'route path of the module'),
                new InputOption('routepath', null, InputOption::VALUE_OPTIONAL, 'tablename of the entity'),
                new InputOption('adminhtml', null, InputOption::VALUE_OPTIONAL, 'create adminhtml or not'),
                new InputOption('gui', null, InputOption::VALUE_OPTIONAL, 'GRID ui component', true),
                new InputOption('api', null, InputOption::VALUE_OPTIONAL, 'API', false),
                new InputOption('readonly', null, InputOption::VALUE_OPTIONAL, 'read only', false),
                new InputOption('per_store', null, InputOption::VALUE_OPTIONAL, 'per store', false),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar generate-model-crud</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $executor = new Crud($this->getIO());
        $this->path = Application::GEN;
        $filename = '.jeeves.yaml';
        if (file_exists($filename)) {
            $config = $executor->readConfig($filename);
        }

        $executor->execute($this->path, $config);

        //reset
        $this->acl = [];
        $this->admin = null;
        $this->menu = [];
        $this->di = [];
        $this->guiList = [];
        $this->db = [];

        foreach ($config as $vendor => $mod) {
            if (in_array($vendor, self::NEW_CONFIG)) {
                $io = $this->getIO();
                $io->write('<warning>Please update to v1 or later</warning>');

                return 1;
            }
            foreach ($mod as $module => $ent) {
                foreach ($ent as $entity => $config) {
                    if (in_array($entity, self::NEW_CONFIG)) {
                        $io = $this->getIO();
                        $io->write('<warning>Please update to v1 or later</warning>');

                        return 1;
                    }
                    $this->genModule($vendor, $module, $entity, $config);
                }
            }
        }

        //xml
        $this->genAdminAcl($this->acl);
        $this->genAdminRoute($this->admin);
        $this->genAdminMenu($this->menu);
        $this->genDBSchema($this->db);
        $this->genEvents($this->events);
        $this->genDI($this->di);
        $this->genModuleXml();

        $this->genRegistration();

        // CS
        $this->runCodeStyleFixer();

        return 0;
    }

    private function getConfig(InputInterface $input): array
    {
        $executor = new Crud();
        $filename = '.jeeves.yaml';
        if (file_exists($filename)) {
            return $executor->readConfig($filename);
        }

        $io = $this->getIO();
        $v = strtolower($input->getArgument('vendor'));
        $m = strtolower($input->getArgument('module'));
        $e = strtolower($input->getArgument('name'));
        $fullname = $v . '/' . $m;
        $fullname = $io->askAndValidate(
            'Package name (<vendor>/<name>) [<comment>' . $fullname . '</comment>]: ',
            function ($value) use ($fullname) {
                if (null === $value) {
                    return $fullname;
                }
                if (!preg_match('{^[a-zA-Z]+/[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The package name ' . $value . ' is invalid'
                            . 'and have a vendor name, a forward slash, '
                            . 'and a package name'
                    );
                }

                return $value;
            },
            null,
            $fullname
        );
        list($v, $m) = explode('/', $fullname);

        $e = $io->askAndValidate(
            'Entity name (<entity>) [<comment>' . $e . '</comment>]: ',
            function ($value) use ($e) {
                if (null === $value) {
                    return $e;
                }
                if (!preg_match('{^[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The entity name ' . $value . ' is invalid'
                    );
                }

                return $value;
            },
            null,
            $e
        );

        $routepath = $input->getOption('routepath') ? $input->getOption('routepath') : $m;
        $tablename = $input->getOption('tablename') ? $input->getOption('tablename') : $v . '_' . $m . '_' . $e;
        $api = (bool) $input->getOption('api');
        $gui = (bool) $input->getOption('gui');
        $readonly = (bool) $input->getOption('readonly');
        $withStore = (bool) $input->getOption('per_store');

        return [
            $v => [
                $m => [
                    $e => [
                        'gui' => $gui,
                        'api' => $api,
                        'readonly' => $readonly,
                        'per_store' => $withStore,
                        'columns' => [
                            'id' => [
                                'type' => 'int',
                                'identity' => true,
                                'unsigned' => true,
                                'comment' => $e . ' ID',
                            ],
                        ],
                        'tablename' => strtolower($tablename),
                        'route' => [
                            'admin' => strtolower($routepath),
                        ],
                    ],
                ],
            ],
        ];
    }

    private function genAdminAcl($entities)
    {
        $this->writeFile(
            $this->path . '/etc/acl.xml',
            $this->getXmlManager()->generateAdminAcl($entities, $this->getFullname(), $this->module)
        );
    }

    private function genAdminLayouts($generator, $entity)
    {
        $parent = $this->getModuleLowercase() . '_' . $this->getEntityLowercase($entity);

        $uiComponent = $parent . '_listing';
        $path = $parent . '_index';
        $this->writeFile(
            $this->path . '/view/adminhtml/layout/' . $path . '.xml',
            $generator->generateAdminLayoutIndex($uiComponent)
        );

        if (!$this->readonly) {
            $editUiComponent = $parent . '_edit';
            $path = $parent . '_edit';
            $this->writeFile(
                $this->path . '/view/adminhtml/layout/' . $path . '.xml',
                $generator->generateAdminLayoutEdit($editUiComponent)
            );

            $uiComponent = $parent . '_new';
            $path = $parent . '_new';
            $this->writeFile(
                $this->path . '/view/adminhtml/layout/' . $path . '.xml',
                $generator->generateAdminLayoutNew($uiComponent, $editUiComponent)
            );
        }
    }

    private function genAdminUI($generator, $entity, $routepath, $fields)
    {
        if (!$this->readonly) {
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

        $filePath = $this->path . '/Model/' . ucfirst($entity) . '/';
        $fileName = 'DataProvider';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->getProvider(
                $entity,
                '\\' . $this->getNamespace() . '\Model\\ResourceModel\\' . ucfirst($entity) . '\\Collection',
                $this->getNamespace() . '\Model\\ResourceModel\\' . ucfirst($entity) . '\\CollectionFactory',
                $this->getNamespace(),
                $fileName,
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase($entity)
            )
        );

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

        if (!$this->readonly) {
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
    }

    private function genGridCollection($generator, $entity)
    {
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

    private function genAdminRoute($path)
    {
        if (!$path) {
            return;
        }
        $this->writeFile(
            $this->path . '/etc/adminhtml/routes.xml',
            $this->getXmlManager()->generateAdminRoute($path, $this->getFullname(), $this->module)
        );
    }

    private function genAdminMenu($entities)
    {
        $this->writeFile(
            $this->path . '/etc/adminhtml/menu.xml',
            $this->getXmlManager()->generateAdminMenu(
                $entities,
                $this->getFullname(),
                $this->module
            )
        );
    }

    private function genDI($entities)
    {
        $this->writeFile(
            $this->path . '/etc/di.xml',
            $this->getXmlManager()->generateDI(
                $this->guiList,
                $entities,
                $this->getNamespace(),
                $this->module
            )
        );
    }

    private function genDBSchema($db)
    {
        $this->writeFile(
            $this->path . '/etc/db_schema.xml',
            $this->getXmlManager()->generateSchema($db)
        );
    }

    private function genEvents($events)
    {
        if (count($events) > 0) {
            $this->writeFile(
                $this->path . '/etc/events.xml',
                $this->getXmlManager()->generateEvents($events)
            );
        }
    }

    private function genModuleXml()
    {
        $this->writeFile(
            $this->path . '/etc/module.xml',
            $this->getXmlManager()->generateModule($this->getFullname())
        );
    }

    private function genRegistration()
    {
        $filePath = $this->path . '/';
        $fileName = 'registration';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            '\Magento\Framework\Component\ComponentRegistrar::register(' . PHP_EOL .
            '   \Magento\Framework\Component\ComponentRegistrar::MODULE,' . PHP_EOL .
            '   \'' . $this->getFullname() . '\',' . PHP_EOL .
            '   __DIR__' . PHP_EOL . ');'
        );
    }

    private function genAPI($generator, $entity)
    {
        $this->writeFile(
            $this->path . '/etc/webapi.xml',
            $generator->generateAPI(
                $entity,
                $this->getNamespace() . '\\Api\\' . ucfirst($entity) . 'RepositoryInterface',
                $this->getEntityAcl($entity),
                $this->module . ucfirst($entity)
            )
        );
    }

    private function genModule($vendor, $module, $entity, $config)
    {
        $this->vendor = $vendor;
        $this->module = $module;
        $this->api = $config['api'] ?? false;
        $this->gui = $config['gui'] ?? true;
        $this->readonly = $config['readonly'] ?? false;
        $this->withStore = $config['per_store'] ?? false;

        $tablename = $config['tablename'] ??
            $this->getConverter()->camelCaseToSnakeCase($this->vendor)
                . '_' . $this->getModuleLowercase()
                . '_' . $this->getEntityLowercase($entity);

        if (!isset($config['route'])) {
            $config['route'] = [];
        }

        if (!isset($config['route']['admin']) || !$config['route']['admin']) {
            $config['route']['admin'] = $this->getModuleLowercase();
        }

        $routepath = $config['route']['admin'];
        $fields = $config['columns'];
        $entityTag = $config['cache_tag'] ?? strtolower(substr($module, 0, 3) . '_' . substr($entity, 0, 1));

        // interface
        $interGenerator = new \Mygento\Jeeves\Generators\Crud\Interfaces();
        $this->genModelInterface($interGenerator, $entity, $entityTag, $fields);
        $this->genModelRepositoryInterface($interGenerator, $entity);
        $this->genModelSearchInterface($interGenerator, $entity);

        // model
        $modelGenerator = new \Mygento\Jeeves\Generators\Crud\Model();
        $this->genModel($modelGenerator, ucfirst($entity), $fields);
        $this->genResourceModel($modelGenerator, ucfirst($entity), $tablename);
        $this->genResourceCollection($modelGenerator, ucfirst($entity));

        // repository
        $repoGenerator = new \Mygento\Jeeves\Generators\Crud\Repository();
        $this->genRepo($repoGenerator, ucfirst($entity));

        if ($this->withStore) {
            $this->genReadHandler($modelGenerator, ucfirst($entity));
            $this->genSaveHandler($modelGenerator, ucfirst($entity));
            $this->getRepoFilter($repoGenerator, ucfirst($entity));
            $interface = implode('\\', [
                $this->getNamespace(),
                'Api',
                'Data',
                ucfirst($entity) . 'Interface',
            ]);
            $event = strtolower(str_replace('\\', '_', $interface));
            $eventName = implode('_', [
                'legacy',
                $this->getVendorLowercase(),
                $this->getModuleLowercase(),
                $this->getEntityLowercase($entity),
            ]);
            $this->events[] = [
                'event' => $event . '_save_before',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'before_save',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\BeforeEntitySave',
                ]],
            ];
            $this->events[] = [
                'event' => $event . '_save_after',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'after_save',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\AfterEntitySave',
                ]],
            ];
            $this->events[] = [
                'event' => $event . '_delete_before',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'before_delete',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\BeforeEntityDelete',
                ]],
            ];
            $this->events[] = [
                'event' => $event . '_delete_after',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'after_delete',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\AfterEntityDelete',
                ]],
            ];
        }

        if ($this->gui) {
            // controllers
            $controllerGenerator = new \Mygento\Jeeves\Generators\Crud\AdminController();
            $this->genAdminAbstractController($controllerGenerator, $entity);
            $this->genAdminViewController($controllerGenerator, ucfirst($entity));
            if (!$this->readonly) {
                $this->genAdminEditController($controllerGenerator, ucfirst($entity));
                $this->genAdminSaveController($controllerGenerator, ucfirst($entity));
                $this->genAdminDeleteController($controllerGenerator, ucfirst($entity));
                $this->genAdminNewController($controllerGenerator, ucfirst($entity));
                $this->genAdminInlineController($controllerGenerator, ucfirst($entity));
                $this->genAdminMassController($controllerGenerator, ucfirst($entity));
            }
        }

        // Layout
        if ($this->gui) {
            $layoutGenerator = new \Mygento\Jeeves\Generators\Crud\Layout();
            $this->genAdminLayouts($layoutGenerator, $entity);
        }

        //UI
        if ($this->gui) {
            $uiGenerator = new \Mygento\Jeeves\Generators\Crud\UiComponent();
            $this->genAdminUI($uiGenerator, $entity, $routepath, $fields);
            $this->genGridCollection($uiGenerator, ucfirst($entity));
        }

        // xml
        $this->acl[] = $entity;
        if ($this->gui) {
            $this->admin = $routepath;
            $this->menu[$entity] = $routepath;
            $this->guiList[$entity] = $tablename;
        }
        if ($this->api) {
            $apiGenerator = new \Mygento\Jeeves\Generators\Crud\Api();
            $this->genAPI($apiGenerator, $entity);
        }
        $this->di[$entity] = [
            'store' => $this->withStore,
            'table' => $tablename,
            'id' => 'id',
        ];
        if (!empty($config) && $tablename) {
            $this->db[$tablename] = $config;
            if ($this->withStore) {
                $this->db[$tablename . '_store'] = [
                    'columns' => [
                        'entity_id' => [
                            'type' => 'int',
                            'unsigned' => true,
                            'nullable' => false,
                            'pk' => true,
                            'comment' => 'Entity ID',
                        ],
                        'store_id' => [
                            'type' => 'smallint',
                            'unsigned' => true,
                            'nullable' => false,
                            'pk' => true,
                            'comment' => 'Store ID',
                        ],
                    ],
                    'fk' => [
                        'FK_' . strtoupper($entity) . '_STORE_ID' => [
                            'column' => 'store_id',
                            'referenceTable' => 'store',
                            'referenceColumn' => 'store_id',
                        ],
                        'FK_' . strtoupper($entity) . '_ENT_ID' => [
                            'column' => 'entity_id',
                            'referenceTable' => $tablename,
                            'referenceColumn' => 'id',
                            'indexName' => 'IX_ENT_ID',
                        ],
                    ],
                    'indexes' => [
                        'IX_' . strtoupper($entity) . '_STORE_ID' => [
                            'columns' => [
                                'store_id',
                            ],
                        ],
                    ],
                ];
            }
        }
    }

    private function genRepo(\Mygento\Jeeves\Generators\Crud\Repository $generator, $entityName)
    {
        $filePath = $this->path . '/Model/';
        $fileName = $entityName . 'Repository';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genRepository(
                $fileName,
                implode(' ', [
                    $this->getConverter()->getEntityName($this->module),
                    $this->getConverter()->getEntityName($entityName),
                ]),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $namePath . 'Model\\ResourceModel\\' . $entityName,
                $namePath . 'Model\\ResourceModel\\' . $entityName . '\\Collection',
                $namePath . 'Api\\Data\\' . $entityName . 'SearchResultsInterface',
                $namePath . 'Api\\Data\\' . $entityName . 'Interface',
                $this->getNamespace(),
                $this->withStore
            )
        );
    }

    private function genModelInterface($generator, $entityName, $tag, $fields)
    {
        $filePath = $this->path . '/Api/Data/';
        $fileName = ucfirst($entityName) . 'Interface';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelInterface(
                $fileName,
                $this->getNamespace(),
                $tag,
                $fields,
                $this->withStore
            )
        );
    }

    private function genModelRepositoryInterface($generator, $entityName)
    {
        $filePath = $this->path . '/Api/';
        $fileName = ucfirst($entityName) . 'RepositoryInterface';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelRepositoryInterface(
                $entityName,
                $namePath . ucfirst($entityName) . 'Interface',
                $namePath . ucfirst($entityName) . 'SearchResultsInterface',
                $fileName,
                $this->getNamespace()
            )
        );
    }

    private function genModelSearchInterface($generator, $entityName)
    {
        $filePath = $this->path . '/Api/Data/';
        $fileName = ucfirst($entityName) . 'SearchResultsInterface';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelSearchInterface(
                $entityName,
                $fileName,
                $namePath . ucfirst($entityName) . 'Interface',
                $this->getNamespace()
            )
        );
    }

    private function genModel($generator, $entityName, $fields)
    {
        $filePath = $this->path . '/Model/';
        $fileName = $entityName;
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModel(
                $fileName,
                $namePath . $entityName . 'Interface',
                '\\' . $this->getNamespace() . '\\Model\ResourceModel' . '\\' . $entityName,
                $this->getNamespace(),
                $this->getEventName($entityName),
                $fields,
                $this->withStore
            )
        );
    }

    private function genReadHandler($generator, $entity)
    {
        $filePath = $this->path . '/Model/ResourceModel/' . ucfirst($entity) . '/Relation/Store/';
        $fileName = 'ReadHandler';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genReadHandler(
                $entity,
                $namePath . 'Model\\ResourceModel\\' . ucfirst($entity),
                $this->getNamespace()
            )
        );
    }

    private function genSaveHandler($generator, $entity)
    {
        $filePath = $this->path . '/Model/ResourceModel/' . ucfirst($entity) . '/Relation/Store/';
        $fileName = 'SaveHandler';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genSaveHandler(
                $entity,
                ucfirst($entity) . 'Interface',
                $namePath . 'Model\\ResourceModel\\' . ucfirst($entity),
                $this->getNamespace()
            )
        );
    }

    private function getRepoFilter(\Mygento\Jeeves\Generators\Crud\Repository $generator, $entity)
    {
        $filePath = $this->path . '/Model/SearchCriteria/';
        $fileName = ucfirst($entity) . 'StoreFilter';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->getRepoFilter(
                $fileName,
                $entity,
                $this->getNamespace()
            )
        );
    }

    private function genResourceModel($generator, $entityName, $table, $key = 'id')
    {
        $filePath = $this->path . '/Model/ResourceModel/';
        $fileName = $entityName;
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceModel(
                $fileName,
                $table,
                $key,
                $this->getNamespace(),
                ucfirst($entityName) . 'Interface',
                $this->withStore
            )
        );
    }

    private function genResourceCollection($generator, $entityName)
    {
        $filePath = $this->path . '/Model/ResourceModel/' . $entityName . '/';
        $fileName = 'Collection';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceCollection(
                $entityName,
                '\\' . $this->getNamespace() . '\\Model' . '\\' . $entityName,
                '\\' . $this->getNamespace() . '\\Model\\ResourceModel' . '\\' . $entityName,
                $this->getNamespace(),
                ucfirst($entityName) . 'Interface',
                $this->withStore
            )
        );
    }

    private function genAdminViewController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Index';
        $namePath = '\\' . $this->getNamespace() . '\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminViewController(
                $entityName,
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase($entityName),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminEditController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Edit';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminEditController(
                $entityName,
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase($entityName),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $namePath . 'Api\\Data\\' . $entityName . 'Interface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminSaveController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Save';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminSaveController(
                $entityName,
                $this->getModuleLowercase() . '_' . $this->getEntityLowercase($entityName),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $namePath . 'Api\\Data\\' . $entityName . 'Interface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminDeleteController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Delete';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminDeleteController(
                $entityName,
                $this->getNamespace()
            )
        );
    }

    private function genAdminNewController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'NewAction';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminNewController(
                $entityName,
                $fileName,
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminInlineController($generator, $entityName)
    {
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
                $this->getNamespace()
            )
        );
    }

    private function genAdminMassController($generator, $entityName)
    {
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
                $this->getNamespace()
            )
        );
    }

    private function genAdminAbstractController($generator, $entity)
    {
        $filePath = $this->path . '/Controller/Adminhtml/';
        $fileName = ucfirst($entity);
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminAbstractController(
                $fileName,
                $this->getFullname(),
                $this->getEntityAcl($entity),
                $namePath . 'Api\\' . ucfirst($entity) . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function getNamespace()
    {
        return ucfirst($this->vendor) . '\\' . ucfirst($this->module);
    }

    private function getFullname()
    {
        return ucfirst($this->vendor) . '_' . ucfirst($this->module);
    }

    private function getEntityAcl($entity)
    {
        return $this->getFullname() . '::' . $this->getEntityLowercase($entity);
    }

    private function getModuleLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->module);
    }

    private function getVendorLowercase()
    {
        return $this->getConverter()->camelCaseToSnakeCase($this->vendor);
    }

    private function getEntityLowercase($entity)
    {
        return $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($entity);
    }

    private function getEventName($entity)
    {
        return implode('_', [
            $this->getVendorLowercase(),
            $this->getConverter()->camelCaseToSnakeCaseNoUnderscore($this->module),
            $this->getConverter()->camelCaseToSnakeCase($entity),
        ]);
    }
}
