<?php

namespace Mygento\Jeeves\Console\Command;

use Mygento\Jeeves\Console\Application;
use Mygento\Jeeves\Model\Crud;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModelCrud extends BaseCommand
{
    private $path;

    protected function configure()
    {
        $this
            ->setName('generate-model-crud')
            ->setAliases(['generate_model_crud', 'crud'])
            ->setDescription('Generate Model Crud')
            ->setDefinition([
                new InputArgument('module', InputArgument::OPTIONAL, 'Name of the module'),
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the entity'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
                new InputOption('tablename', null, InputOption::VALUE_OPTIONAL, 'route path of the module'),
                new InputOption('routepath', null, InputOption::VALUE_OPTIONAL, 'tablename of the entity'),
                new InputOption('adminhtml', null, InputOption::VALUE_OPTIONAL, 'create adminhtml or not'),
                new InputOption('gui', null, InputOption::VALUE_OPTIONAL, 'GRID ui component', true),
                new InputOption('api', null, InputOption::VALUE_OPTIONAL, 'API', false),
                new InputOption('readonly', null, InputOption::VALUE_OPTIONAL, 'read only', false),
                new InputOption('per_store', null, InputOption::VALUE_OPTIONAL, 'per store', false),
                new InputOption('config_file', null, InputOption::VALUE_OPTIONAL, 'config file', null),
                new InputOption('path', null, InputOption::VALUE_OPTIONAL, 'path', null),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar generate-model-crud</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $executor = new Crud($this->getIO());
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
            'Entity name (<entity>) [<comment>' . $e . '</comment>]: ',
            function ($value) use ($e) {
                if (null === $value) {
                    return $e;
                }
                if (!preg_match('{^[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The entity name ' . $value . ' is invalid'
                    );
                }

                return $value;
            },
            null,
            $e
        );

        $routepath = $input->getOption('routepath') ? $input->getOption('routepath') : $m;
        $tablename = $input->getOption('tablename') ? $input->getOption('tablename') : $v . '_' . $m . '_' . $e;
        $api = (bool) $input->getOption('api');
        $gui = (bool) $input->getOption('gui');
        $readonly = (bool) $input->getOption('readonly');
        $withStore = (bool) $input->getOption('per_store');

        return [
            $v => [
                $m => [
                    'settings' => [
                        'admin_route' => strtolower($routepath),
                    ],
                    'entities' => [
                        $e => [
                            'gui' => $gui,
                            'api' => $api,
                            'readonly' => $readonly,
                            'per_store' => $withStore,
                            'columns' => [
                                'id' => [
                                    'type' => 'int',
                                    'identity' => true,
                                    'unsigned' => true,
                                    'comment' => $e . ' ID',
                                ],
                            ],
                            'tablename' => strtolower($tablename),
                        ],
                    ],
                ],
            ],
        ];
    }
}
