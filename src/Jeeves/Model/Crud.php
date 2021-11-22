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
                foreach ($ent as $type => $entities) {
                    if ($type !== 'entities') {
                        continue;
                    }

                    $moduleResult = $this->generateEntities($entities, $vendor, $module);
                    $result->updateAcl($moduleResult->getAcl());
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

    private function setGlobalSettings(array $config)
    {
        $this->version = $config['settings']['version'] ?? '2.3';
        $this->typehint = $config['settings']['typehint'] ?? false;
    }

    private function generateEntities(array $entities, string $vendor, string $module): Crud\Result
    {
        $result = new Crud\Result();
        $acl = [];
        $mod = new Module($vendor, $module);
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
            $acl = array_merge($acl, $entityResult->getAcl());
        }

        $result->updateAcl(
            [
                $mod->getFullname() => new Acl(
                    $mod->getFullname() . '::root',
                    $mod->getPrintName(),
                    $acl
                ),
            ]
        );

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
        $dbschema = [];
        $di = [];
        $events = [];
        $menu = [];

        $result = new Crud\Result();

        $result->updateAcl($acl);
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
}
