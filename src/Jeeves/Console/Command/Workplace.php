<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Workplace extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('new-workplace')
            ->setAliases(['workplace'])
            ->setDescription('Create workplace')
            ->setDefinition([
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the entity'),
                new InputArgument('repo', InputArgument::OPTIONAL, 'Project repository url'),
              ])
            ->setHelp(
                <<<EOT
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
            'Project Name [<comment>' . $folder . '</comment>]: ',
            function ($value) use ($folder) {
                if (null === $value) {
                    return $folder;
                }
                return $value;
            },
            null,
            $folder
        );
        $repo = strtolower($input->getArgument('repo'));
        $repo = $io->askAndValidate(
            'Project repository [<comment>' . $repo . '</comment>]: ',
            function ($value) use ($repo) {
                if (null === $value) {
                    return $repo;
                }
                return $value;
            },
            null,
            $repo
        );
        if (!is_dir($folder . '-project')) {
            mkdir($folder . '-project');
        }
        $workplaceFolder = $folder . '-project' . DIRECTORY_SEPARATOR . $folder . '-workplace';
        try {
            $io->write(sprintf('Cloning: <info>%s</info>.', 'workplace'));
            $repository = \Gitonomy\Git\Admin::cloneTo(
                $workplaceFolder,
                'https://github.com/mygento/workplace.git',
                false
            );
        } catch (\Gitonomy\Git\Exception\RuntimeException $e) {
            $io->writeError($e->getMessage());
        }
        try {
            $io->write(sprintf('Cloning: <info>%s</info>.', $repo));
            $repository = \Gitonomy\Git\Admin::cloneTo(
                $folder . '-project' . DIRECTORY_SEPARATOR . $folder,
                $repo,
                false
            );
        } catch (\Gitonomy\Git\Exception\RuntimeException $e) {
            $io->writeError($e->getMessage());
        }
        $srcFolder = $workplaceFolder . DIRECTORY_SEPARATOR . 'src';
        if (!is_dir($srcFolder)) {
            $io->write(sprintf('Creating symlink to: <info>%s</info>.', $folder));
            symlink('../' . $folder, $srcFolder);
            return;
        }
        if (!is_link($srcFolder) &&
            (
                !(new \FilesystemIterator($srcFolder))->valid() ||
                count(scandir($srcFolder)) <= 3
            )
        ) {
            $io->write(sprintf('Creating symlink to: <info>%s</info>.', $folder));
            unlink($srcFolder . DIRECTORY_SEPARATOR . '.keep');
            rmdir($srcFolder);
            symlink('../' . $folder, $srcFolder);
        }
    }
}
