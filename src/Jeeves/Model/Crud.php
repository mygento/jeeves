<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\IO\IOInterface;
use Symfony\Component\Yaml\Yaml;

class Crud
{
    private $magentoVersion;
    private $configVersion;
    private $phpVersion;
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
            if ($vendor === 'settings' || $vendor === 'version') {
                continue;
            }
            foreach ($mod as $module => $ent) {
                $modEntity = new Module($vendor, $module, $this->phpVersion);
                if (isset($ent['settings'])) {
                    $modEntity->setConfig($ent['settings']);
                }

                $entities = $ent['entities'] ?? null;
                $this->configVersion = 1;

                if ($entities === null) {
                    $this->configVersion = 0;
                    $entities = $ent;
                }

                if (empty($entities)) {
                    continue;
                }

                $moduleResult = $this->generateEntities($entities, $modEntity);
                $result->updateAclEntities($moduleResult->getAclEntities());
                $result->updateAclConfigs($moduleResult->getAclConfigs());
                $result->updateAdminRoute($moduleResult->getAdminRoute());
                $result->updateMenu($moduleResult->getMenu());
                $result->updateDbSchema($moduleResult->getDbSchema());
                $result->updateEvents($moduleResult->getEvents());
                $result->updateDi($moduleResult->getDi());
                $result->updateWebApi($moduleResult->getWebApi());
                $result->setModule($modEntity->getFullname());
            }
        }

        if ($this->global) {
            return $result;
        }

        $this->generateConfigs($result);
    }

    public function generateConfigs(Crud\Result $result)
    {
        $generator = new Crud\Configs($this->io);
        $generator->generate($result);
    }

    private function setGlobalSettings(array $config)
    {
        $this->magentoVersion = $config['settings']['version'] ?? '2.4';
        $this->phpVersion = $config['settings']['php_version'] ?? PHP_VERSION;
    }

    private function generateEntities(array $entities, Module $mod): Crud\Result
    {
        $result = new Crud\Result();
        $aclEntity = [];
        $menuEntity = [];
        $dbSchema = [];
        $events = [];
        $webapi = [];

        $entityList = [];

        $gui = false;
        foreach ($entities as $entityName => $config) {
            $entity = new Crud\Entity();
            $entity->setIO($this->io);
            $entity->setPath($this->path);
            $entity->setVersion($this->magentoVersion);
            $entity->setPhpVersion($mod->getPhpVersion());

            $entity->setModule($mod);
            $entity->setName($entityName);

            if ($this->configVersion === 0) {
                $config['cacheable'] = true;
                $config['settings']['php_version'] = '7.3.0';
            }

            $entity->setConfig($config);

            if ($entity->hasApi()) {
                $gui = true;
            }

            $entityList[] = $entity;

            $entityResult = $this->generate($entity);
            $aclEntity = array_merge($aclEntity, $entityResult->getAclEntities());
            $menuEntity = array_merge($menuEntity, $entityResult->getMenu());
            $dbSchema = array_merge($dbSchema, $entityResult->getDbSchema());
            $events = array_merge($events, $entityResult->getEvents());
            $webapi = array_merge($webapi, $entityResult->getWebApi());
        }

        $result->updateDi($this->generateDependency($entityList));
        $result->updateAclEntities(
            [
                $mod->getFullname() => new Acl(
                    $mod->getFullname() . '::root',
                    $mod->getFullPrintName(),
                    $aclEntity
                ),
            ]
        );
        $result->updateAclConfigs(
            [
                new Acl(
                    $mod->getFullname() . '::config',
                    $mod->getFullPrintName()
                ),
            ]
        );

        if ($gui) {
            $result->updateAdminRoute(
                [
                    $mod->getFullname() => new AdminRoute(
                        $mod->getRouteName(),
                        $mod->getFullname(),
                        $mod->getAdminRoute()
                    ),
                ]
            );
        }

        if (!empty($menuEntity)) {
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
        }
        $result->updateDbSchema($dbSchema);
        $result->updateEvents($events);
        $result->updateWebApi($webapi);

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

        if ($entity->hasGui()) {
            $this->generateControllers($entity);
            $this->generateAdminLayouts($entity);

            $this->generateAdminUI($entity);
        }

        $acl = [new Acl($entity->getEntityAcl(), $entity->getEntityAclTitle())];
        $dbschema = $this->generateDbSchema($entity);
        $events = $this->generateEvents($entity);
        $webapi = $this->generateWebApi($entity);
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
        $result->updateEvents($events);
        if ($entity->hasGui()) {
            $result->updateMenu($menu);
        }
        $result->updateWebApi($webapi);

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
        $comment = $entity->getComment() ?: $entity->getPrintName() . ' Table';
        $indexes = $entity->getIndexes();
        $fk = $entity->getFk();

        $result = [
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

        if ($entity->withStore()) {
            $storeColumns = $generator->getColumnsPerStore();
            $result[] = new DbTable(
                $entity->getTablename() . '_store',
                $storeColumns,
                $comment . ' With Store',
                $generator->getIndexesPerStore($entity),
                $generator->getFkPerStore($entity),
                $generator->getPrimaryPerStore()
            );
        }

        return $result;
    }

    private function generateWebApi(Crud\Entity $entity): array
    {
        $generator = new Crud\Apis();

        return $generator->generateApi($entity);
    }

    private function generateEvents(Crud\Entity $entity): array
    {
        $generator = new Crud\Event();

        return $generator->generateEvents($entity);
    }

    private function generateDependency(array $entities): array
    {
        $generator = new Crud\Dependency();

        return $generator->generate($entities);
    }
}
