<?php

namespace Project;

use Mygento\Jeeves\Console\Application as App;
use Mygento\Jeeves\Console\Command\EmptyProject;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class ProjectTest extends \PHPUnit\Framework\TestCase
{
    private const V = 'project';

    private $path;

    private $commandTester;

    protected function setUp(): void
    {
        $application = new Application();
        $application->add(new EmptyProject());
        $command = $application->find('project-template');

        $this->commandTester = new CommandTester($command);
        $this->path = App::GEN . DIRECTORY_SEPARATOR . self::V;
    }

    public function testProjectBasic()
    {
        $this->commandTester->execute([
            'name' => 'Sample',
            'repo' => '',
            'path' => $this->path,
        ]);
        $this->checkJson('composer.json');
        $this->checkFile('app/etc/config.php');
        $this->checkFile('config/deploy.rb');
        $this->checkFile('.editorconfig');
        $this->checkJson('.eslintrc.json');
        $this->checkFile('.php_cs');
        $this->checkFile('.scss-lint.yml');
        $this->checkJson('composer.json');
        $this->checkJson('package.json');
        $this->checkFile('Gemfile');
        $this->checkFile('grumphp.yml');
        $this->checkFile('gulpfile.js');
    }

    private function checkFile($file)
    {
        $this->assertFileEquals(
            'test/Expectations/Project/' . $file,
            $this->path . '/' . $file
        );
    }

    private function checkXml($file)
    {
        $this->assertXmlFileEqualsXmlFile(
            'test/Expectations/Project/' . $file,
            $this->path . '/' . $file
        );
    }

    private function checkJson($file)
    {
        $this->assertJsonFileEqualsJsonFile(
            'test/Expectations/Project/' . $file,
            $this->path . '/' . $file
        );
    }
}
