<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Filesystem\Filesystem;

use Mygento\Jeeves\Console\Application;
use Mygento\Jeeves\IO\NullIO;

abstract class BaseCommand extends Command
{
    /**
     * @var IOInterface
     */
    private $io;

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        if (null === $this->io) {
            $application = $this->getApplication();
            if ($application instanceof Application) {
                /* @var $application    Application */
                $this->io = $application->getIO();
            } else {
                $this->io = new NullIO();
            }
        }
        return $this->io;
    }

    protected function writeFile($path, $content)
    {
        $fs = new Filesystem();
        return $fs->dumpFile($path, $content);
    }
}
