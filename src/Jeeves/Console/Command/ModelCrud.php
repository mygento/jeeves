<?php
namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class ModelCrud extends BaseCommand
{
    private $vendor;
    private $module;
    private $path;

    protected function getNamespace()
    {
        return ucfirst($this->vendor) . '\\' . ucfirst($this->module);
    }

    protected function getFullname()
    {
        return ucfirst($this->vendor) . '_' . ucfirst($this->module);
    }

    protected function configure()
    {
        $this
            ->setName('generate_model_crud')
            ->setDescription('Generate Model Crud')
            ->setDefinition([
                new InputArgument('module', InputArgument::REQUIRED, 'Name of the module'),
                new InputArgument('name', InputArgument::REQUIRED, 'Name of the entity'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
                new InputOption('tablename', null, InputOption::VALUE_OPTIONAL, 'route path of the module'),
                new InputOption('routepath', null, InputOption::VALUE_OPTIONAL, 'tablename of the entity'),
                new InputOption('adminhtml', false, InputOption::VALUE_OPTIONAL, 'create adminhtml or not'),
            ])
            ->setHelp(
                <<<EOT
<info>php jeeves.phar generate_model_crud</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->path = 'generated';
        $io = $this->getIO();
        $vendor = strtolower($input->getArgument('vendor'));
        $module = strtolower($input->getArgument('module'));
        $entity = strtolower($input->getArgument('name'));
        $fullname = $vendor . '/' . $module;
        $fullname = $io->askAndValidate(
            'Package name (<vendor>/<name>) [<comment>' . $fullname . '</comment>]: ',
            function ($value) use ($fullname) {
                if (null === $value) {
                    return $fullname;
                }
                if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The package name ' . $value . ' is invalid, it should be lowercase '
                        . 'and have a vendor name, a forward slash, '
                        . 'and a package name, matching: [a-z0-9_.-]+/[a-z0-9_.-]+'
                    );
                }
                return $value;
            },
            null,
            $fullname
        );

        list($this->vendor, $this->module) = explode('/', $fullname);

        $entity = $io->askAndValidate(
            'Entity name (<entity>) [<comment>' . $entity . '</comment>]: ',
            function ($value) use ($entity) {
                if (null === $value) {
                    return $entity;
                }
                if (!preg_match('{^[a-z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The entity name ' . $value . ' is invalid, it should be lowercase and matching: [a-z]'
                    );
                }
                return $value;
            },
            null,
            $entity
        );

        $routepath = $input->getOption('routepath') ? $input->getOption('routepath') : $module;
        $tablename = $input->getOption('tablename') ? $input->getOption('tablename') : $vendor . '_' . $module . '_' . $entity;

        // interface
        $interGenerator = new \Mygento\Jeeves\Generators\Crud\Interfaces();
        $this->genModelInterface($interGenerator, $entity);
        $this->genModelRepositoryInterface($interGenerator, $entity);
        $this->genModelSearchInterface($interGenerator, $entity);

        // model
        $modelGenerator = new \Mygento\Jeeves\Generators\Crud\Model();
        $this->genModel($modelGenerator, ucfirst($entity));
        $this->genResourceModel($modelGenerator, ucfirst($entity), $tablename);
        $this->genResourceCollection($modelGenerator, ucfirst($entity));

        // repository
        $repoGenerator = new \Mygento\Jeeves\Generators\Crud\Repository();
        $this->genRepo($repoGenerator, ucfirst($entity));

        // controllers
        $controllerGenerator = new \Mygento\Jeeves\Generators\Crud\AdminController();
        $this->genAdminAbstractController($controllerGenerator, ucfirst($entity));
        $this->genAdminViewController($controllerGenerator, ucfirst($entity));
        $this->genAdminEditController($controllerGenerator, ucfirst($entity));
        $this->genAdminSaveController($controllerGenerator, ucfirst($entity));
        $this->genAdminDeleteController($controllerGenerator, ucfirst($entity));
        $this->genAdminNewController($controllerGenerator, ucfirst($entity));
        $this->genAdminInlineController($controllerGenerator, ucfirst($entity));
        $this->genAdminMassController($controllerGenerator, ucfirst($entity));

        // xml
//      $this->genAdminRoute($routepath);
//      $this->genAdminLayouts($entity);
//      $this->genAdminAcl($entity);
        $this->runCodeStyleFixer();
    }

    private function genRepo($generator, $entityName)
    {
        $filePath = $this->path . '/Model/';
        $fileName = $entityName . 'Repository';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genRepository(
                $fileName,
                ucfirst($this->module) . ' ' . $entityName,
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $namePath . 'Model\\ResourceModel\\' . $entityName,
                $namePath . 'Model\\ResourceModel\\' . $entityName . '\\Collection',
                $namePath . 'Model\\' . $entityName,
                $namePath . 'Api\\Data\\' . $entityName . 'SearchResultsInterface',
                $namePath . 'Api\\Data\\' . $entityName . 'Interface',
                $this->getNamespace()
            )
        );
    }

    private function genModelInterface($generator, $entityName)
    {
        $filePath = $this->path . '/Api/Data/';
        $fileName = ucfirst($entityName) . 'Interface';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelInterface($fileName, $this->getNamespace())
        );
    }

    private function genModelRepositoryInterface($generator, $entityName)
    {
        $filePath = $this->path . '/Api/';
        $fileName = ucfirst($entityName) . 'RepositoryInterface';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelRepositoryInterface(
                $entityName,
                $namePath . ucfirst($entityName) . 'Interface',
                $namePath . ucfirst($entityName) . 'SearchResultsInterface',
                $fileName,
                $this->getNamespace()
            )
        );
    }

    private function genModelSearchInterface($generator, $entityName)
    {
        $filePath = $this->path . '/Api/Data/';
        $fileName = ucfirst($entityName) . 'SearchResultsInterface';
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModelSearchInterface(
                $entityName,
                $fileName,
                $namePath . ucfirst($entityName) . 'Interface',
                $this->getNamespace()
            )
        );
    }

    private function genModel($generator, $entityName)
    {
        $filePath = $this->path . '/Model/';
        $fileName = $entityName;
        $namePath = '\\' . $this->getNamespace() . '\\Api\\Data\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genModel(
                $fileName,
                $namePath . $entityName . 'Interface',
                '\\' . $this->getNamespace() . '\\Model\ResourceModel' . '\\' . $entityName,
                $this->getNamespace()
            )
        );
    }

    private function genResourceModel($generator, $entityName, $table, $key = 'id')
    {
        $filePath = $this->path . '/Model/ResourceModel/';
        $fileName = $entityName;
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceModel(
                $fileName,
                $table,
                $key,
                $this->getNamespace()
            )
        );
    }

    private function genResourceCollection($generator, $entityName)
    {
        $filePath = $this->path . '/Model/ResourceModel/' . $entityName . '/';
        $fileName = 'Collection';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genResourceCollection(
                $entityName,
                '\\' . $this->getNamespace() . '\\Model' . '\\' . $entityName,
                '\\' . $this->getNamespace() . '\\Model\\ResourceModel' . '\\' . $entityName,
                $this->getNamespace()
            )
        );
    }

    private function genAdminViewController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Index';
        $namePath = '\\' . $this->getNamespace() . '\\';

        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminViewController(
                $entityName,
                $this->module . '_' . strtolower($entityName),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminEditController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Edit';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminEditController(
                $entityName,
                $this->module . '_' . strtolower($entityName),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $namePath . 'Model\\' . $entityName,
                $this->getNamespace()
            )
        );
    }

    private function genAdminSaveController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Save';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminSaveController(
                $entityName,
                $this->module . '_' . strtolower($entityName),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $namePath . 'Model\\' . $entityName,
                $this->getNamespace()
            )
        );
    }

    private function genAdminDeleteController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'Delete';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminDeleteController(
                $entityName,
                $this->getNamespace()
            )
        );
    }

    private function genAdminNewController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'NewAction';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminNewController(
                $entityName,
                $fileName,
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminInlineController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'InlineEdit';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminInlineController(
                $entityName,
                $fileName,
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminMassController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/' . $entityName . '/';
        $fileName = 'MassDelete';
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminMassController(
                $entityName,
                $fileName,
                $namePath . 'Model\\ResourceModel\\' . $entityName . '\\CollectionFactory',
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    private function genAdminAbstractController($generator, $entityName)
    {
        $filePath = $this->path . '/Controller/Adminhtml/';
        $fileName = $entityName;
        $namePath = '\\' . $this->getNamespace() . '\\';
        $this->writeFile(
            $filePath . $fileName . '.php',
            '<?php' . PHP_EOL . PHP_EOL .
            $generator->genAdminAbstractController(
                $entityName,
                $this->getFullname(),
                $namePath . 'Api\\' . $entityName . 'RepositoryInterface',
                $this->getNamespace()
            )
        );
    }

    protected function genAdminRoute($path)
    {
        $xml = $this->getXmlManager()->generateAdminRoute($this->module, $path, $this->getFullname());
        $this->writeFile('generated/etc/adminhtml/routes.xml', $xml);
    }

    protected function genAdminLayouts($entity)
    {
        $uiComponent = $this->module . '_' . $entity . '_listing';
        $uiComponent = 'cms_block_listing';
        $xml = $this->getXmlManager()->generateAdminLayoutIndex($uiComponent);
        $path = $this->module . '_' . $entity . '_index';
        $this->writeFile('generated/view/adminhtml/layout/' . $path . '.xml', $xml);

        // $path = $module.'_'.$entity.'_edit';
        // $service = new \Sabre\Xml\Service();
        // $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        // $xml = $service->write('config', function ($writer) use ($path, $module) {
        //     $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:View/Layout/etc/page_configuration.xsd');
        // });
        // $this->writeFile('generated/view/adminhtml/layout/'.$path.'.xml', $xml);
        //
        // $path = $module.'_'.$entity.'_edit';
        // $service = new \Sabre\Xml\Service();
        // $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        // $xml = $service->write('config', function ($writer) use ($path, $module) {
        //     $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:View/Layout/etc/page_configuration.xsd');
        // });
        // $this->writeFile('generated/view/adminhtml/layout/'.$path.'.xml', $xml);
    }

    public function genAdminAcl($entity)
    {
        $xml = $this->getXmlManager()->generateAdminAcl($this->getFullname(), $this->module, $entity);
        $this->writeFile('generated/etc/acl.xml', $xml);
    }
}
