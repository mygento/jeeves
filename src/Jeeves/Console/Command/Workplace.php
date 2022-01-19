<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class Workplace extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('workplace')
            ->setDescription('Create workplace')
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the project'),
                new InputOption('path', null, InputOption::VALUE_OPTIONAL, 'path', '.'),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar workplace</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getIO();
        $name = strtolower($input->getArgument('name'));
        $name = $io->askAndValidate(
            'Project Name [<comment>' . $name . '</comment>]: ',
            function ($value) use ($name) {
                if (null === $value) {
                    return $name;
                }

                return $value;
            },
            null,
            $name
        );

        $path = $input->getOption('path') . '/';

        $this->writeFile($path . 'package.json', $this->generateWorkplace($name));

        return 0;
    }

    private function generateWorkplace(string $project): string
    {
        return json_encode(
            [
                'name' => $project,
                'private' => true,
                'version' => '1.0.0',
                'description' => '',
                'scripts' => [
                    'start' => 'mage-workplace start',
                    'test' => 'mage-workplace test',
                    'stop' => 'mage-workplace stop',
                    'delete' => 'mage-workplace delete',
                    'install' => 'mage-workplace install',
                ],
                'dependencies' => [
                    'mage-workplace' => '~1.0.0-beta16',
                ],
                'workplace' => [
                    'type' => 'magento2',
                ],
            ],
            JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES
        );
    }
}
