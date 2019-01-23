<?php

namespace Mygento\Jeeves\Console;

use Mygento\Jeeves\Factory;
use Mygento\Jeeves\IO\ConsoleIO;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Util\ErrorHandler;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    const VERSION = '0.0.13';
    const GEN = 'generate';

    /**
     * @var IOInterface
     */
    protected $io;

    public function __construct()
    {
        parent::__construct('Jeeves', self::VERSION);
    }

    /**
     * {@inheritDoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $output = Factory::createOutput();
        }
        return parent::run($input, $output);
    }

    /**
     * {@inheritDoc}
     */
    public function doRun(InputInterface $input, OutputInterface $output)
    {
        $io = $this->io = new ConsoleIO($input, $output, $this->getHelperSet());
        ErrorHandler::register($io);

        try {
            $result = parent::doRun($input, $output);
            restore_error_handler();
            return $result;
        } catch (\Exception $e) {
            restore_error_handler();
            throw $e;
        }
    }

    /**
     * Initializes all commands.
     */
    protected function getDefaultCommands()
    {
        $commands = array_merge(parent::getDefaultCommands(), [
            //new Command\PaymentGateway(),
            new Command\ModelCrud(),
            new Command\SelfUpdate(),
            new Command\Workplace(),
            new Command\EmptyProject(),
        ]);
        return $commands;
    }

    /**
     * @return IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }
}
