<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class Workplace extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('new-workplace')
            ->setAliases(array('workplace'))
            ->setDescription('Create workplace')
            ->setDefinition(array(
                new InputArgument('name', InputArgument::REQUIRED, 'Name of the entity'),
              ))
            ->setHelp(<<<EOT
<info>php jeeves.phar new-workplace</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();
        // Clone to a non-bare repository
        $folder = strtolower($input->getArgument('name'));
        $folder = $io->askAndValidate(
            'Folder [<comment>'.$folder.'</comment>]: ',
            function ($value) use ($folder) {
                if (null === $value) {
                    return $folder;
                }
                return $value;
            },
            null,
            $folder
        );
        $repository = \Gitonomy\Git\Admin::cloneTo($folder, 'https://github.com/mygento/workplace.git', false);
    }
}
