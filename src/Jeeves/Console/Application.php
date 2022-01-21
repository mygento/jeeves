<?php

namespace Mygento\Jeeves\Console;

use Mygento\Jeeves\Factory;
use Mygento\Jeeves\IO\ConsoleIO;
use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Util\ErrorHandler;
use SelfUpdate\SelfUpdateCommand;
use Symfony\Component\Console\Application as BaseApplication;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class Application extends BaseApplication
{
    public const GEN = 'generate';
    public const VERSION = '1.0.0-rc.1';
    public const NAME = 'Jeeves';

    /**
     * @var IOInterface
     */
    protected $io;

    public function __construct()
    {
        parent::__construct('Jeeves', self::VERSION);
    }

    /**
     * {@inheritdoc}
     */
    public function run(InputInterface $input = null, OutputInterface $output = null)
    {
        if (null === $output) {
            $output = Factory::createOutput();
        }

        return parent::run($input, $output);
    }

    /**
     * {@inheritdoc}
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
     * @return IOInterface
     */
    public function getIO()
    {
        return $this->io;
    }

    /**
     * Initializes all commands.
     */
    protected function getDefaultCommands()
    {
        $selfUpdate = new SelfUpdateCommand(self::NAME, self::VERSION, 'mygento/jeeves');

        return array_merge(parent::getDefaultCommands(), [
            new Command\Generate(),
            //new Command\PaymentGateway(),
            new Command\ModelCrud(),
            new Command\Workplace(),
            // new Command\EmptyProject(),
            new Command\ShippingModule(),
            $selfUpdate,
        ]);
    }
}
