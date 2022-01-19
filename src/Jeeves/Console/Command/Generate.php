<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Yaml\Yaml;

class Generate extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate')
            ->setDescription('Generate via config')
            ->setHelp(
                <<<EOT
<info>php jeeves.phar generate</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $filename = '.jeeves.yaml';

        if (!file_exists($filename)) {
            return 1;
        }
        $config = Yaml::parseFile($filename);

        $crud = false;
        $shipping = false;
        $payment = false;
        foreach ($config as $vendor => $mod) {
            foreach ($mod as $module => $ent) {
                if (isset($ent['crud'])) {
                    $crud = true;
                }
                if (isset($ent['shipping'])) {
                    $shipping = true;
                }
            }
        }
        if ($crud) {
            $command = $this->getApplication()->find('generate-model-crud');

            $arguments = [
                'command' => 'generate-model-crud',
            ];

            $input = new ArrayInput($arguments);
            $command->run($input, $output);
        }

        return 0;
    }
}
