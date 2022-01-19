<?php

namespace Mygento\Jeeves\Console\Command;

use Humbug\SelfUpdate\Updater;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SelfUpdate extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(['selfupdate'])
            ->setDescription('Updates jeeves to the latest version.')
            ->setHelp(
                <<<EOT
<info>php jeeves.phar self-update</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = $this->getIO();
        $updater = new Updater(null, false);
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('mygento/jeeves');
        $updater->getStrategy()->setPharName('jeeves.phar');
        $updater->getStrategy()->setCurrentLocalVersion(\Mygento\Jeeves\Console\Application::VERSION);

        try {
            $result = $updater->update();
            if ($result) {
                $new = $updater->getNewVersion();
                $old = $updater->getOldVersion();
                $io->write(sprintf('Updated from %s to %s', $old, $new));
            } else {
                $io->write('No update needed!');
            }
        } catch (\Exception $e) {
            $io->writeError($e->getMessage());

            return 1;
        }

        return 0;
    }
}
