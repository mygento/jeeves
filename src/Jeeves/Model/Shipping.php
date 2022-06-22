<?php

namespace Mygento\Jeeves\Model;

use Mygento\Jeeves\Generators\Shipping\Carrier;
use Mygento\Jeeves\Generators\Shipping\Helper;
use Mygento\Jeeves\IO\IOInterface;
use Symfony\Component\Yaml\Yaml;

class Shipping extends Generator
{
    protected $path;
    protected $io;
    private $globalTypehint;
    private $mod;
    private $global;

    // private $magentoVersion;

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
        $result = new Shipping\Result();
        $result->setPath($path);

        foreach ($config as $vendor => $mod) {
            if ($vendor === 'settings' || $vendor === 'version') {
                continue;
            }
            foreach ($mod as $module => $ent) {
                $modEntity = new Module($vendor, $module, $this->globalTypehint);
                if (isset($ent['settings'])) {
                    $modEntity->setConfig($ent['settings']);
                }

                $carrier = $ent['shipping']['code'] ?? null;

                if (empty($carrier)) {
                    continue;
                }

                $moduleResult = $this->generate($carrier, $modEntity);
                $result->updateCarrierConfigs($moduleResult->getCarrierConfigs());
                $result->updateDefaultConfigs($moduleResult->getDefaultConfigs());
                $result->setModule($modEntity->getFullname());
            }
        }

        if ($this->global) {
            return $result;
        }

        $this->generateConfigs($result);

        return 0;
    }

    public function generateConfigs(Shipping\Result $result)
    {
        $generator = new Shipping\Configs($this->io);
        $generator->generate($result);
    }

    private function generate(string $carrier, Module $mod): Shipping\Result
    {
        $result = new Shipping\Result();
        $this->mod = $mod;

        $this->generateHelper($carrier);
        $this->generateCarrier($carrier);
        $this->generateClient();
        $this->generateService();

        $result->updateCarrierConfigs([
            $carrier => [
                'active' => 0,
                'title' => $carrier,
            ],
        ]);
        $result->updateDefaultConfigs([
            $carrier => [
                'active' => 0,
                'name' => $carrier,
                'title' => $carrier,
                'debug' => '0',
                'test' => '1',
                'model' => $this->mod->getNamespace() . '\Model\Carrier',
                'order_status' => [
                    'autoshipping' => '0',
                    'track_check' => '0',
                    'track_cron' => '*/15 * * * *',
                ],
            ],
        ]);

        return $result;
    }

    private function setGlobalSettings(array $config)
    {
        // $this->magentoVersion = $config['settings']['version'] ?? '2.4';
        $this->globalTypehint = $config['settings']['typehint'] ?? true;
    }

    private function generateHelper(string $carrier)
    {
        $generator = new Helper();
        $filePath = $this->path . '/Helper/';
        $fileName = 'Data';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genHelper(
                strtolower($carrier),
                $this->mod->getNamespace()
            )
        );
    }

    private function generateCarrier(string $carrier)
    {
        $generator = new Carrier();
        $filePath = $this->path . '/Model/';
        $fileName = 'Carrier';
        $namePath = '\\' . $this->mod->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genCarrier(
                strtolower($carrier),
                $namePath . 'Model\\Service',
                $namePath . 'Model\\Carrier',
                $namePath . 'Helper\\Data',
                $this->mod->getNamespace()
            )
        );
    }

    private function generateClient()
    {
        $generator = new Carrier();
        $filePath = $this->path . '/Model/';
        $fileName = 'Client';
        $namePath = '\\' . $this->mod->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genClient(
                $namePath . 'Helper\\Data',
                $this->mod->getNamespace()
            )
        );
    }

    private function generateService()
    {
        $generator = new Carrier();
        $filePath = $this->path . '/Model/';
        $fileName = 'Service';
        $namePath = '\\' . $this->mod->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genService(
                $namePath . 'Model\\Client',
                $this->mod->getNamespace()
            )
        );
    }
}
