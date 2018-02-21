<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;

use Humbug\SelfUpdate\Updater;

class SelfUpdate extends BaseCommand
{

    protected function configure()
    {
        $this
            ->setName('self-update')
            ->setAliases(array('selfupdate'))
            ->setDescription('Updates jeeves to the latest version.')
            ->setHelp(<<<EOT
<info>php jeeves.phar self-update</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $updater = new Updater(null, false);
        $updater->setStrategy(Updater::STRATEGY_GITHUB);
        $updater->getStrategy()->setPackageName('mygento/jeeves');
        $updater->getStrategy()->setPharName('jeeves.phar');
        $updater->getStrategy()->setStability('any');
        #$updater->getStrategy()->setCurrentLocalVersion('v1.0.1');
        try {
            $result = $updater->update();
            echo $result ? "Updated!\n" : "No update needed!\n";
        } catch (\Exception $e) {
            echo $e->getMessage();
            echo "Well, something happened! Either an oopsie or something involving hackers.\n";
            exit(1);
        }
    }
}
