<?php

namespace Project;

use Mygento\Jeeves\Console\Command\EmptyProject;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectTest extends \PHPUnit\Framework\TestCase
{
    protected function setUp()
    {
        $application = new \Symfony\Component\Console\Application();
        $application->add(new EmptyProject());
        $command = $application->find('project-template');
        $this->commandTester = new CommandTester($command);
    }

    public function testProjectBasic()
    {
        $this->commandTester->execute([
            'name'=>'Sample',
            'repo'=>'',
            'path' => \Mygento\Jeeves\Console\Application::GEN
        ]);
        $this->checkJson('composer.json');
        $this->checkFile('app/etc/config.php');
        $this->checkFile('config/deploy.rb');
        $this->checkFile('.editorconfig');
        $this->checkJson('.eslintrc.json');
        $this->checkFile('.php_cs');
        $this->checkFile('.scss-lint.yml');
        $this->checkFile('.shippable.yml');
        $this->checkJson('composer.json');
        $this->checkJson('package.json');
        $this->checkFile('Gemfile');
        $this->checkFile('grumphp.yml');
        $this->checkFile('gulpfile.js');
    }

    private function checkFile($file)
    {
        $this->assertFileEquals(
            \Mygento\Jeeves\Console\Application::GEN.'/'.$file,
            'test/Expectations/Project/'.$file
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            \Mygento\Jeeves\Console\Application::GEN.'/'.$file,
            'test/Expectations/Project/'.$file
        );
    }

    private function checkJson($file)
    {
        $this->assertJsonFileEqualsJsonFile(
            \Mygento\Jeeves\Console\Application::GEN.'/'.$file,
            'test/Expectations/Project/'.$file
        );
    }
}
