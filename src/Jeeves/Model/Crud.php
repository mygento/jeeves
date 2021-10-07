<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\IO\IOInterface;
use Symfony\Component\Yaml\Yaml;

class Crud
{
    private $version;

    private $typehint;

    private $io;

    private $path;

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

            $entity->generate();
        }
    }
}
