<?php

namespace Mygento\Jeeves\Console\Command;

use Mygento\CS\Config\Module;
use Mygento\Jeeves\Console\Application;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\IO\NullIO;
use Mygento\Jeeves\Util\XmlManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

abstract class BaseCommand extends Command
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @var XmlManager
     */
    private $xmlManager;

    /**
     * @var \Mygento\Jeeves\Generators\Crud\Common
     */
    private $converter;

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        if (null === $this->io) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                $this->io = $application->getIO();
            } else {
                $this->io = new NullIO();
            }
        }

        return $this->io;
    }

    /**
     * @return XmlManager
     */
    public function getXmlManager()
    {
        if (null === $this->xmlManager) {
            $this->xmlManager = new XmlManager();
        }

        return $this->xmlManager;
    }

    protected function writeFile($path, $content)
    {
        $fs = new Filesystem();
        $io = $this->getIO();
        $io->write(sprintf('Creating: <info>%s</info>.', $path));

        return $fs->dumpFile($path, $content);
    }

    protected function runCodeStyleFixer()
    {
        $finder = \PhpCsFixer\Finder::create()
            ->name('*.php')
            ->in(\Mygento\Jeeves\Console\Application::GEN);

        $config = new Module();
        $config->setFinder($finder);

        $resolver = new \PhpCsFixer\Console\ConfigurationResolver($config, [], getcwd(), new \PhpCsFixer\ToolInfo());
        $errorsManager = new \PhpCsFixer\Error\ErrorsManager();
        $runner = new \PhpCsFixer\Runner\Runner(
            $finder,
            $resolver->getFixers(),
            $resolver->getDiffer(),
            null,
            $errorsManager,
            $resolver->getLinter(),
            $resolver->isDryRun(),
            $resolver->getCacheManager(),
            $resolver->getDirectory(),
            $resolver->shouldStopOnViolation()
        );

        $io = $this->getIO();
        $io->write(sprintf('Fixing CS'));
        $changed = $runner->fix();

        $invalidErrors = $errorsManager->getInvalidErrors();
        $exceptionErrors = $errorsManager->getExceptionErrors();
        $lintErrors = $errorsManager->getLintErrors();

        if (count($changed) > 0) {
            $this->listChanges($changed);
        }

        if (count($invalidErrors) > 0) {
            $this->listErrors('linting before fixing', $invalidErrors);
        }
        if (count($exceptionErrors) > 0) {
            $this->listErrors('fixing', $exceptionErrors);
        }
        if (count($lintErrors) > 0) {
            $this->listErrors('linting after fixing', $lintErrors);
        }
    }

    protected function listErrors($process, $errors)
    {
        $io = $this->getIO();
        $io->write(['', sprintf(
            'Files that were not fixed due to errors reported during %s:',
            $process
        )]);
        foreach ($errors as $i => $error) {
            $io->writeError(sprintf('%4d) %s', $i + 1, $error->getFilePath()));
            $e = $error->getSource();
        }
    }

    protected function listChanges($changed)
    {
        $i = 0;
        $output = '';
        foreach ($changed as $file => $fixResult) {
            ++$i;
            $output .= sprintf('%s', $file);
            $output .= sprintf(' (<comment>%s</comment>) ', implode(', ', $fixResult['appliedFixers']));
            $output .= PHP_EOL;
        }
        $io = $this->getIO();
        $io->write($output);
    }

    /**
     * get Converter.
     *
     * @return \Mygento\Jeeves\Generators\Crud\Common
     */
    protected function getConverter()
    {
        if (null === $this->converter) {
            $this->converter = new \Mygento\Jeeves\Generators\Crud\Common();
        }

        return $this->converter;
    }
}
