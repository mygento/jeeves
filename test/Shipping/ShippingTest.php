<?php

namespace Shipping;

use Mygento\Jeeves\Console\Application as App;
use Mygento\Jeeves\Console\Command\ShippingModule;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CrudTest extends \PHPUnit\Framework\TestCase
{
    private const V = 'shipping';

    private $path;

    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new ShippingModule());
        $command = $application->find('generate-shipping');

        $this->commandTester = new CommandTester($command);
        $this->path = App::GEN . DIRECTORY_SEPARATOR . self::V;
    }

    public function testCrudBasic()
    {
        $this->commandTester->execute([
            '--config_file' => '.jeeves.phpunit_v1.yaml',
            '--path' => $this->path,
        ]);
        $this->checkFile('Helper/Data.php');
        $this->checkModels();
        $this->checkXml('etc/adminhtml/system.xml');
    }

    private function checkModels()
    {
        $this->checkFile('Model/Client.php');
        $this->checkFile('Model/Carrier.php');
        $this->checkFile('Model/Service.php');
    }

    private function checkFile($file)
    {
        $this->assertFileEquals(
            'test/Expectations/Shipping/' . $file,
            $this->path . '/' . $file,
            '',
            false,
            false
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            'test/Expectations/Shipping/' . $file,
            $this->path . '/' . $file,
        );
        $this->assertFileEquals(
            'test/Expectations/Shipping/' . $file,
            $this->path . '/' . $file,
            '',
            false,
            false
        );
    }
}
