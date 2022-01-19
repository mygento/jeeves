<?php

namespace Mygento\Jeeves\Console\Command;

use Mygento\Jeeves\Console\Application;
use Mygento\Jeeves\Model\Shipping;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ShippingModule extends BaseCommand
{
    private $vendor;

    private $path;

    private $module;

    private $entity;

    protected function configure()
    {
        $this
            ->setName('generate-shipping')
            ->setAliases(['generate_shipping', 'shipping'])
            ->setDescription('Generate Shipping Model')
            ->setDefinition([
                new InputArgument('module', InputArgument::OPTIONAL, 'Name of the module'),
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the method'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
                new InputOption('config_file', null, InputOption::VALUE_OPTIONAL, 'config file', null),
                new InputOption('path', null, InputOption::VALUE_OPTIONAL, 'path', null),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar generate-shipping</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $executor = new Shipping($this->getIO());
        $this->path = Application::GEN;
        $filename = '.jeeves.yaml';
        $config = [];

        if ($input->getOption('config_file')) {
            $filename = $input->getOption('config_file');
        }

        if ($input->getOption('path')) {
            $this->path = $input->getOption('path');
        }

        if (file_exists($filename)) {
            $config = $executor->readConfig($filename);
        }

        if (empty($config)) {
            $config = $this->getInputConfig($input);
        }

        if (empty($config)) {
            $io = $this->getIO();
            $io->write('<warning>Empty Config</warning>');

            return 1;
        }

        $executor->execute($this->path, $config);
        $this->runCodeStyleFixer();

        return 0;
    }

    private function getInputConfig(InputInterface $input): array
    {
        $io = $this->getIO();
        $v = strtolower($input->getArgument('vendor'));
        $m = strtolower($input->getArgument('module'));
        $e = strtolower($input->getArgument('name'));
        $fullname = $v . '/' . $m;
        $fullname = $io->askAndValidate(
            'Package name (<vendor>/<name>) [<comment>' . $fullname . '</comment>]: ',
            function ($value) use ($fullname) {
                if (null === $value) {
                    return $fullname;
                }
                if (!preg_match('{^[a-zA-Z]+/[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The package name ' . $value . ' is invalid'
                        . 'and have a vendor name, a forward slash, '
                        . 'and a package name'
                    );
                }

                return $value;
            },
            null,
            $fullname
        );
        list($v, $m) = explode('/', $fullname);

        $e = $io->askAndValidate(
            'Method name (<method>) [<comment>' . $m . '</comment>]: ',
            function ($value) use ($m) {
                if (null === $value) {
                    return $m;
                }
                if (!preg_match('{^[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The method name ' . $value . ' is invalid'
                    );
                }

                return $value;
            },
            null,
            $m
        );

        return [
            $v => [
                $m => [
                    'shipping' => $e,
                ],
            ],
        ];
    }
}
