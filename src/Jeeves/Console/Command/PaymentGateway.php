<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;


use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\Model\Method;
use Memio\Model\Argument;

class PaymentGateway extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate_payment_gateway')
            ->setDescription('Generate Payment Gateway')
            ->setDefinition(array(
                new InputOption('name', null, InputOption::VALUE_REQUIRED, 'Name of the gateway'),
                new InputOption('codename', null, InputOption::VALUE_OPTIONAL, 'Name of the gateway codename'),
              )
            )
            ->setHelp(<<<EOT
<info>php jeeves.phar generate_payment_gateway</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $file = File::make('x')
        ->setStructure(
            Object::make('Vendor\Project\MyService')
                ->addProperty(new Property('createdAt'))
                ->addProperty(new Property('filename'))
                ->addMethod(
                    Method::make('__construct')
                        ->addArgument(new Argument('DateTime', 'createdAt'))
                        ->addArgument(new Argument('string', 'filename'))
                )
        );
        // Generate the code and display in the console
        $prettyPrinter = Build::prettyPrinter();
        $generatedCode = $prettyPrinter->generateCode($file);
        $this->writeFile('generated/MyService.php', $generatedCode);
    }
}
