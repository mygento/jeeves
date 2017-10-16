<?php

namespace Mygento\Jeeves\Console\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;


use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\Model\Method;
use Memio\Model\Argument;

class ModelCrud extends BaseCommand
{
    protected function configure()
    {
        $this
            ->setName('generate_model_crud')
            ->setDescription('Generate Model Crud')
            ->setDefinition(array(
                new InputArgument('module', InputArgument::REQUIRED, 'Name of the module'),
                new InputArgument('name', InputArgument::REQUIRED, 'Name of the entity'),
                new InputArgument('vendor', InputArgument::OPTIONAL, 'Vendor of the module', 'mygento'),
                new InputOption('tablename', null, InputOption::VALUE_OPTIONAL, 'route path of the module'),
                new InputOption('routepath', null, InputOption::VALUE_OPTIONAL, 'tablename of the entity'),
              ))
            ->setHelp(<<<EOT
<info>php jeeves.phar generate_model_crud</info>
EOT
            )
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->getIO();
        $vendor = strtolower($input->getArgument('vendor'));
        $module = strtolower($input->getArgument('module'));
        $entity = strtolower($input->getArgument('name'));
        $fullname = $vendor.'/'.$module;
        $fullname = $io->askAndValidate(
            'Package name (<vendor>/<name>) [<comment>'.$fullname.'</comment>]: ',
            function ($value) use ($fullname) {
                if (null === $value) {
                    return $fullname;
                }
                if (!preg_match('{^[a-z0-9_.-]+/[a-z0-9_.-]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The package name '.$value.' is invalid, it should be lowercase and have a vendor name, a forward slash, and a package name, matching: [a-z0-9_.-]+/[a-z0-9_.-]+'
                    );
                }
                return $value;
            },
            null,
            $fullname
        );

        list($vendor, $module) = explode('/', $fullname);

        $entity = $io->askAndValidate(
            'Entity name (<entity>) [<comment>'.$entity.'</comment>]: ',
            function ($value) use ($entity) {
                if (null === $value) {
                    return $entity;
                }
                echo $entity;
                if (!preg_match('{^[a-z]+$}', $value)) {
                    throw new \InvalidArgumentException(
                        'The entity name '.$value.' is invalid, it should be lowercase and matching: [a-z]'
                    );
                }
                return $value;
            },
            null,
            $entity
        );

        $routepath = $input->getOption('routepath') ? $input->getOption('routepath') : $module;
        $tablename = $input->getOption('tablename') ? $input->getOption('tablename') : $vendor.'_'.$module.'_'.$entity;
        $classNamespace = ucfirst($vendor).'\\'.ucfirst($module);

        $this->genModel($classNamespace, ucfirst($entity));
        $this->genResourceModel($classNamespace, ucfirst($entity), 'mygento_keys');
        $this->genResourceCollection($classNamespace, ucfirst($entity));
        $this->genAdminRoute(ucfirst($vendor).'_'.ucfirst($module), $routepath);
        $this->genAdminControllers($classNamespace, ucfirst($vendor).'_'.ucfirst($module), ucfirst($entity));
    }

    protected function genModel($module, $model)
    {
          $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Model');
          $class = $namespace->addClass($model);
          $class->setExtends('\Magento\Framework\Model\AbstractModel');
          $method = $class->addMethod('_construct')
              ->addComment('Initialize '.$model.' model')
              ->setVisibility('protected')
              ->setBody('$this->_init(\\'.$module.'\\Model\ResourceModel'.'\\'.$model.'::class);');
          $this->writeFile('generated/Model/'.$model.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genResourceModel($module, $model, $table, $key = 'id')
    {
          $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Model\ResourceModel');
          $class = $namespace->addClass($model);
          $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');
          $method = $class->addMethod('_construct')
              ->addComment('Initialize '.$model.' resource model')
              ->setVisibility('protected')
              ->setBody('$this->_init(\''.$table.'\', \''.$key.'\');');
          $this->writeFile('generated/Model/ResourceModel/'.$model.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genResourceCollection($module, $model)
    {
        $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Model\ResourceModel\\'.$model);
        $class = $namespace->addClass('Collection');
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
        $method = $class->addMethod('_construct')
          ->addComment('Initialize '.$model.' resource collection')
          ->setVisibility('protected')
          ->setBody('$this->_init('.PHP_EOL.
            '   '.'\\'.$module.'\\Model'.'\\'.$model.'::class,'.PHP_EOL.
            '   '.'\\'.$module.'\\Model\ResourceModel'.'\\'.$model.'::class'.PHP_EOL.
            ');');
        $this->writeFile('generated/Model/ResourceModel/'.$model.'/Collection.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genAdminRoute($module, $path)
    {
        $service = new \Sabre\Xml\Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];
        $xml = $service->write('config', function ($writer) use ($path, $module) {
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:App/etc/routes.xsd');
            $writer->write([
              'name' => 'router',
              'attributes' => [
                'id' => 'admin'
              ],
              'value' => [
                  [
                    'name' => 'route',
                    'attributes' => [
                      'id' => strtolower($module),
                      'frontName' => $path,
                    ],
                    'value' => [
                      'name' => 'module',
                      'attributes' => [
                        'name' => $module,
                      ],
                    ]
                  ]
              ]
            ]);
        });
        $this->writeFile('generated/etc/adminhtml/routes.xml', $xml);
    }

    protected function genAdminControllers($module, $module2, $entity)
    {
        $this->genAdminAbstractController($module, $module2, $entity);
        $this->genAdminViewController($module, $module2, $entity);

        $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Controller\Adminhtml\\'.$entity);
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Index.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Edit.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Save.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Delete.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genAdminViewController($module, $module2, $entity)
    {
        $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Controller\Adminhtml\\'.$entity);
        $class = $namespace->addClass('Index');
        $class
            ->setExtends($module.'\Controller\Adminhtml\\'.$entity)
        ;
        $class->addProperty('resultPageFactory')
              ->setVisibility('protected')
              ->addComment(' \Magento\Framework\View\Result\PageFactory')
        ;

        $method = $class->addMethod('__construct')
          ->addComment('@param \Magento\Framework\View\Result\PageFactory $resultPageFactory')
          ->addComment('@param \Magento\Framework\Registry $coreRegistry')
          ->addComment('@param \Magento\Backend\App\Action\Context $context')
          ->setBody('$this->resultPageFactory = resultPageFactory;'.PHP_EOL
                .'parent::__construct($coreRegistry, $context);
          ');

        $method->addParameter('resultPageFactory')->setTypeHint('\Magento\Framework\View\Result\PageFactory');
        $method->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $method->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');


        $method = $class->addMethod('execute')
          ->addComment('Index action')
          ->addComment('')
          ->addComment('@return \Magento\Framework\Controller\ResultInterface')
          ->setBody(' /** @var \Magento\Backend\Model\View\Result\Page $resultPage */'.PHP_EOL
            .'$resultPage = $this->resultPageFactory->create();'.PHP_EOL
            .'$this->initPage($resultPage)->getConfig()->getTitle()->prepend(__(\''.$entity.'\'));'.PHP_EOL.PHP_EOL
            //.'$dataPersistor = $this->_objectManager->get(\Magento\Framework\App\Request\DataPersistorInterface::class);'.PHP_EOL
            //.'$dataPersistor->clear(\'cms_block\');'.PHP_EOL
            .'return $resultPage;');
        $method->addParameter('resultPage');
        //echo $namespace;
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Index.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genAdminAbstractController($module, $module2, $entity)
    {
        $namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Controller\Adminhtml');
        $class = $namespace->addClass($entity);
        $class
            ->setAbstract()
            ->setExtends('\Magento\Backend\App\Action')
        ;
        $class->addConstant('ADMIN_RESOURCE', $module2.'::'.strtolower($entity))
              ->addComment('Authorization level')
              ->addComment('')
              ->addComment('@see _isAllowed()')
        ;
        $class->addProperty('_coreRegistry')
              ->setVisibility('protected')
              ->addComment('Core registry')
              ->addComment('')
              ->addComment('@var \Magento\Framework\Registry')
        ;

        $method = $class->addMethod('__construct')
          ->addComment('@param \Magento\Framework\Registry $coreRegistry')
          ->addComment('@param \Magento\Backend\App\Action\Context $context')
          ->setBody('$this->_coreRegistry = $coreRegistry;'.PHP_EOL.'parent::__construct($context);');

        $method->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $method->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');


        // $method = $class->addMethod('initPage')
        //   ->setVisibility('protected')
        //   ->addComment('@param \Magento\Backend\Model\View\Result\Page $resultPage')
        //   ->addComment('@return \Magento\Backend\Model\View\Result\Page')
        //   ->setBody('$resultPage->setActiveMenu(\'Magento_Cms::cms_block\')'.PHP_EOL
        //   .'->addBreadcrumb(__(\'CMS\'), __(\'CMS\'));'.PHP_EOL
        //   .  'return $resultPage;');
        // $method->addParameter('resultPage');
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }
}
