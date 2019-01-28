<?php

namespace Shipping;

use Mygento\Jeeves\Console\Command\ShippingModule;
use Symfony\Component\Console\Tester\CommandTester;

class CrudTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $application = new \Symfony\Component\Console\Application();
        $application->add(new ShippingModule());
        $command = $application->find('generate-shipping');
        $this->commandTester = new CommandTester($command);
    }

    public function testCrudBasic()
    {
        $this->commandTester->execute([
            'module' => 'Banan',
        ]);
        $this->checkFile('Helper/Data.php');
        $this->checkModels();
        // $this->checkXml('etc/di.xml');
        // $this->checkXml('etc/webapi.xml');
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
            \Mygento\Jeeves\Console\Application::GEN.'/'.$file,
            'test/Expectations/Shipping/'.$file,
            '',
            false,
            false
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            \Mygento\Jeeves\Console\Application::GEN.'/'.$file,
            'test/Expectations/Shipping/'.$file
        );
        $this->assertFileEquals(
            \Mygento\Jeeves\Console\Application::GEN.'/'.$file,
            'test/Expectations/Shipping/'.$file,
            '',
            false,
            false
        );
    }
}
