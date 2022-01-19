<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ShippingModule extends BaseCommand
{
    private $vendor;

    private $path;

    private $module;

    private $entity;

    protected function configure()
    {
        $this
            ->setName('generate-shipping')
            ->setAliases(['generate_shipping', 'shipping'])
            ->setDescription('Generate Shipping Model')
            ->setDefinition([
                new InputArgument('module', InputArgument::REQUIRED, 'Name of the module'),
                new InputArgument('name', InputArgument::OPTIONAL, 'Name of the method'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar generate-shipping</info>
EOT
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->path = \Mygento\Jeeves\Console\Application::GEN;
        $io = $this->getIO();
        $v = strtolower($input->getArgument('vendor'));
        $m = strtolower($input->getArgument('module'));
        $e = strtolower($input->getArgument('name'));
        $fullname = $v . '/' . $m;
        $fullname = $io->askAndValidate(
            'Package name (<vendor>/<name>) [<comment>' . $fullname . '</comment>]: ',
            function ($value) use ($fullname) {
                if (null === $value) {
                    return $fullname;
                }
                if (!preg_match('{^[a-zA-Z]+/[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The package name ' . $value . ' is invalid'
                        . 'and have a vendor name, a forward slash, '
                        . 'and a package name'
                    );
                }

                return $value;
            },
            null,
            $fullname
        );
        list($v, $m) = explode('/', $fullname);

        $e = $io->askAndValidate(
            'Method name (<method>) [<comment>' . $m . '</comment>]: ',
            function ($value) use ($m) {
                if (null === $value) {
                    return $m;
                }
                if (!preg_match('{^[a-zA-Z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The method name ' . $value . ' is invalid'
                    );
                }

                return $value;
            },
            null,
            $m
        );

        $this->vendor = $v;
        $this->module = $m;
        $this->entity = $e;

        //Helper
        $this->genHelper(new \Mygento\Jeeves\Generators\Shipping\Helper());

        //Models
        $generator = new \Mygento\Jeeves\Generators\Shipping\Carrier();
        $this->genCarrier($generator);
        $this->genClient($generator);
        $this->genService($generator);

        //xml
        $this->genSystemXml();

        // CS
        $this->runCodeStyleFixer();

        return 0;
    }

    protected function getNamespace()
    {
        return ucfirst($this->vendor) . '\\' . ucfirst($this->module);
    }

    private function genHelper($generator)
    {
        $filePath = $this->path . '/Helper/';
        $fileName = 'Data';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genHelper(
                strtolower($this->entity),
                $this->getNamespace()
            )
        );
    }

    private function genCarrier($generator)
    {
        $filePath = $this->path . '/Model/';
        $fileName = 'Carrier';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genCarrier(
                strtolower($this->entity),
                $namePath . 'Model\\Service',
                $namePath . 'Model\\Carrier',
                $namePath . 'Helper\\Data',
                $this->getNamespace()
            )
        );
    }

    private function genClient($generator)
    {
        $filePath = $this->path . '/Model/';
        $fileName = 'Client';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genClient(
                $namePath . 'Helper\\Data',
                $this->getNamespace()
            )
        );
    }

    private function genService($generator)
    {
        $filePath = $this->path . '/Model/';
        $fileName = 'Service';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genService(
                $namePath . 'Model\\Client',
                $this->getNamespace()
            )
        );
    }

    private function genSystemXml()
    {
        $this->writeFile(
            $this->path . '/etc/adminhtml/system.xml',
            $this->getXmlManager()->generateShippingSystem(
                strtolower($this->entity),
                ucfirst($this->module),
                $this->getNamespace()
            )
        );
    }
}
