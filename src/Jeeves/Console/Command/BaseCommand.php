<?php

namespace Mygento\Jeeves\Console\Command;

use Mygento\Jeeves\Console\Application;
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
            /** @var $application Application */
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

        $config = \PhpCsFixer\Config::create()
            ->setRules([
                '@PSR2' => true,
                'array_syntax' => ['syntax' => 'short'],
                'concat_space' => ['spacing' => 'one'],
                'include' => true,
                'new_with_braces' => true,
                'no_empty_statement' => true,
                'no_extra_consecutive_blank_lines' => true,
                'no_leading_import_slash' => true,
                'no_leading_namespace_whitespace' => true,
                'no_multiline_whitespace_around_double_arrow' => true,
                'no_multiline_whitespace_before_semicolons' => true,
                'no_singleline_whitespace_before_semicolons' => true,
                'no_trailing_comma_in_singleline_array' => true,
                'no_unused_imports' => true,
                'no_whitespace_in_blank_line' => true,
                'object_operator_without_whitespace' => true,
                'ordered_imports' => true,
                'standardize_not_equals' => true,
                'ternary_operator_spaces' => true,
                // mygento
                'phpdoc_order' => true,
                'phpdoc_types' => true,
                'phpdoc_add_missing_param_annotation' => true,
                'single_quote' => true,
                'standardize_not_equals' => true,
                'ternary_to_null_coalescing' => true,
                'ternary_operator_spaces' => true,
                'lowercase_cast' => true,
                'no_empty_comment' => true,
                'no_empty_phpdoc' => true,
                'return_type_declaration' => true,
            ])->setFinder($finder);

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
