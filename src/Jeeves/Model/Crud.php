<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\IO\IOInterface;
use Symfony\Component\Yaml\Yaml;

class Crud
{
    private $version;

    private $typehint;

    private $path;

    private $io;

    private $global = false;

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    public function setGlobal(bool $status)
    {
        $this->global = $status;
    }

    public function readConfig(string $filename): array
    {
        return Yaml::parseFile($filename);
    }

    public function execute(string $path, array $config)
    {
        $this->path = $path;
        $this->setGlobalSettings($config);
        $result = new Crud\Result();
        $result->setPath($path);

        foreach ($config as $vendor => $mod) {
            if ($vendor === 'settings') {
                continue;
            }
            foreach ($mod as $module => $ent) {
                $modEntity = new Module($vendor, $module);
                if (isset($ent['settings'])) {
                    $modEntity->setConfig($ent['settings']);
                }

                foreach ($ent as $type => $entities) {
                    if ($type !== 'entities') {
                        continue;
                    }

                    $moduleResult = $this->generateEntities($entities, $modEntity);
                    $result->updateAclEntities($moduleResult->getAclEntities());
                    $result->updateAclConfigs($moduleResult->getAclConfigs());
                    $result->updateAdminRoute($moduleResult->getAdminRoute());
                    $result->updateMenu($moduleResult->getMenu());
                    $result->updateDbSchema($moduleResult->getDbSchema());
                    $result->updateEvents($moduleResult->getEvents());
                }
            }
        }
        // $this->genModuleXml();
        // $this->genAdminRoute($this->admin);

        if ($this->global) {
            return $result;
        }

        $this->generateConfigs($result);

        die();
    }

    public function generateConfigs(Crud\Result $result)
    {
        $generator = new Crud\Configs($this->io);
        $generator->generate($result);
    }

    private function generateEvents(Crud\Entity $entity): array
    {
        $events = [];
        if ($entity->withStore()) {
            $event = $entity->getEntityLowercase();
            $eventName = implode('_', [
                'legacy',
                $entity->getEventName($entity->getName()),
            ]);
            $events[] = [
                'event' => $event . '_save_before',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'before_save',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\BeforeEntitySave',
                ]],
            ];
            $events[] = [
                'event' => $event . '_save_after',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'after_save',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\AfterEntitySave',
                ]],
            ];
            $events[] = [
                'event' => $event . '_delete_before',
                'observer' => [[
                    'name' => implode('_', [
                        $eventName,
                        'before_delete',
                    ]),
                    'instance' => 'Magento\Framework\EntityManager\Observer\BeforeEntityDelete',
                ]],
            ];
            $events[] = [
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

        return $events;
    }

    private function setGlobalSettings(array $config)
    {
        $this->version = $config['settings']['version'] ?? '2.3';
        $this->typehint = $config['settings']['typehint'] ?? false;
    }

    private function generateEntities(array $entities, Module $mod): Crud\Result
    {
        $result = new Crud\Result();
        $aclEntity = [];
        $menuEntity = [];
        $dbSchema = [];
        $events = [];

        foreach ($entities as $entityName => $config) {
            $entity = new Crud\Entity();
            $entity->setIO($this->io);
            $entity->setPath($this->path);
            $entity->setVersion($this->version);
            $entity->setTypeHint($this->typehint);
            $entity->setModule($mod);
            $entity->setName($entityName);
            $entity->setConfig($config);

            $entityResult = $this->generate($entity);
            $aclEntity = array_merge($aclEntity, $entityResult->getAclEntities());
            $menuEntity = array_merge($menuEntity, $entityResult->getMenu());
            $dbSchema = array_merge($dbSchema, $entityResult->getDbSchema());
            $events = array_merge($events, $entityResult->getEvents());
        }

        $result->updateAclEntities(
            [
                $mod->getFullname() => new Acl(
                    $mod->getFullname() . '::root',
                    $mod->getPrintName(),
                    $aclEntity
                ),
            ]
        );
        $result->updateAclConfigs(
            [
                new Acl(
                    $mod->getFullname() . '::config',
                    $mod->getPrintName()
                ),
            ]
        );
        $result->updateAdminRoute(
            [
                $mod->getFullname() => new AdminRoute(
                    $mod->getRouteName(),
                    $mod->getFullname(),
                    $mod->getAdminRoute()
                ),
            ]
        );

        $result->updateMenu([
            new Menu(
                $mod->getFullname() . '::root',
                $mod->getPrintName(),
                $mod->getFullname(),
                'Magento_Backend::stores',
                $mod->getFullname() . '::root'
            ),
        ]);
        $result->updateMenu($menuEntity);
        $result->updateDbSchema($dbSchema);
        $result->updateEvents($events);

        return $result;
    }

    private function generate(Crud\Entity $entity): Crud\Result
    {
        if (empty($entity->getConfig())) {
            return new Crud\Result();
        }

        $this->generateInterfaces($entity);
        $this->generateModels($entity);
        $this->generateRepository($entity);

        if ($entity->hasGui($entity)) {
            $this->generateControllers($entity);
            $this->generateAdminLayouts($entity);

            $this->generateAdminUI($entity);
        }

        $acl = [new Acl($entity->getEntityAcl(), $entity->getEntityAclTitle())];
        $dbschema = $this->generateDbSchema($entity);
        $di = [];
        $events = $this->generateEvents($entity);
        $menu = [
            new Menu(
                $entity->getFullname() . '::' . $entity->getEntityLowercase(),
                $entity->getPrintName(),
                $entity->getFullname(),
                $entity->getFullname() . '::root',
                $entity->getFullname() . '::' . $entity->getEntityLowercase(),
                $entity->getModule()->getAdminRoute() . '/' . $entity->getEntityLowercase()
            ),
        ];

        $result = new Crud\Result();

        $result->updateAclEntities($acl);
        $result->updateDbSchema($dbschema);
        $result->updateDi($di);
        $result->updateEvents($events);
        $result->updateMenu($menu);

        return $result;
    }

    private function generateInterfaces(Crud\Entity $entity)
    {
        $generator = new Crud\Interfaces($this->io);
        $generator->generateInterfaces($entity);
    }

    private function generateModels(Crud\Entity $entity)
    {
        $generator = new Crud\Models($this->io);
        $generator->generateModels($entity);
    }

    private function generateRepository(Crud\Entity $entity)
    {
        $generator = new Crud\Repository($this->io);
        $generator->generateRepository($entity);
        $generator->generateSearchResults($entity);

        if ($entity->withStore()) {
            $generator->generateWithStore($entity);
        }
    }

    private function generateControllers(Crud\Entity $entity)
    {
        $generator = new Crud\Controllers($this->io);
        $generator->generateControllers($entity);
    }

    private function generateAdminLayouts(Crud\Entity $entity)
    {
        $generator = new Crud\Layouts($this->io);
        $generator->generateAdminLayouts($entity);
    }

    private function generateAdminUI(Crud\Entity $entity)
    {
        $generator = new Crud\Ui($this->io);
        $generator->generateAdminUI($entity);
        $generator->generateGridCollection($entity);
    }

    private function generateDbSchema(Crud\Entity $entity): array
    {
        $generator = new Crud\Database();

        $columns = $generator->getColumns($entity);
//        $constraints = [];

        $comment = $entity->getPrintName() . ' Table';

        $indexes = $entity->getIndexes();

        $fk = $entity->getFk();

        return [
            new DbTable(
                $entity->getTablename(),
                $columns,
                $comment,
                $indexes,
                $fk,
                [
                    $entity->getPrimaryKey(),
                ]
            ),
        ];
    }
}
