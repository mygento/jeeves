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

    public function __construct(IOInterface $io)
    {
        $this->io = $io;
    }

    public function readConfig(string $filename): array
    {
        return Yaml::parseFile($filename);
    }

    public function execute(string $path, array $config)
    {
        $this->path = $path;
        $this->setGlobalSettings($config);

        foreach ($config as $vendor => $mod) {
            if ($vendor === 'settings') {
                continue;
            }
            // var_dump($vendor);
            foreach ($mod as $module => $ent) {
                // var_dump($module);
                foreach ($ent as $type => $entities) {
                    // var_dump($type);
                    if ($type !== 'entities') {
                        continue;
                    }
                    $this->generateEntities($entities, $vendor, $module);
                }
            }
        }
        die();
    }

    private function setGlobalSettings(array $config)
    {
        $this->version = $config['settings']['version'] ?? '2.3';
        $this->typehint = $config['settings']['typehint'] ?? false;
    }

    private function generateEntities(array $entities, string $vendor, string $module)
    {
        foreach ($entities as $entityName => $config) {
            $entity = new Crud\Entity();
            $entity->setIO($this->io);
            $entity->setPath($this->path);
            $entity->setVersion($this->version);
            $entity->setTypeHint($this->typehint);
            $entity->setVendor($vendor);
            $entity->setModule($module);
            $entity->setName($entityName);
            $entity->setConfig($config);

            $this->generate($entity);
        }
    }

    private function generate(Crud\Entity $entity)
    {
        if (empty($entity->getConfig())) {
            return;
        }

        $this->generateInterfaces($entity);
        $this->generateModels($entity);
        $this->generateRepository($entity);

        if ($entity->hasGui($entity)) {
            $this->generateControllers($entity);
            $this->generateAdminLayouts($entity);

            $this->generateAdminUI($entity);
        }
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
