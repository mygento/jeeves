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

    private $vendor;
    private $module;


    protected function getNamespace()
    {
        return ucfirst($this->vendor).'\\'.ucfirst($this->module);
    }

    protected function getFullname()
    {
        return ucfirst($this->vendor).'_'.ucfirst($this->module);
    }

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

        list($this->vendor, $this->module) = explode('/', $fullname);

        $entity = $io->askAndValidate(
            'Entity name (<entity>) [<comment>'.$entity.'</comment>]: ',
            function ($value) use ($entity) {
                if (null === $value) {
                    return $entity;
                }
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

        // php
        $this->genModel(ucfirst($entity));
        $this->genResourceModel(ucfirst($entity), $tablename);
        $this->genResourceCollection(ucfirst($entity));
        $this->genAdminControllers(ucfirst($entity));

        // xml
        $this->genAdminRoute($routepath);
        $this->genAdminLayouts($entity);
        $this->genAdminAcl($entity);
        $this->runCodeStyleFixer();
    }

    protected function genModel($model)
    {
          $namespace = new \Nette\PhpGenerator\PhpNamespace($this->getNamespace().'\Model');
          $class = $namespace->addClass($model);
          $class->setExtends('\Magento\Framework\Model\AbstractModel');
          $method = $class->addMethod('_construct')
              ->addComment('Initialize '.$model.' model')
              ->setVisibility('protected')
              ->setBody('$this->_init(\\'.$this->getNamespace().'\\Model\ResourceModel'.'\\'.$model.'::class);');
          $this->writeFile('generated/Model/'.$model.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genResourceModel($model, $table, $key = 'id')
    {
          $namespace = new \Nette\PhpGenerator\PhpNamespace($this->getNamespace().'\Model\ResourceModel');
          $class = $namespace->addClass($model);
          $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\AbstractDb');
          $method = $class->addMethod('_construct')
              ->addComment('Initialize '.$model.' resource model')
              ->setVisibility('protected')
              ->setBody('$this->_init(\''.$table.'\', \''.$key.'\');');
          $this->writeFile('generated/Model/ResourceModel/'.$model.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genResourceCollection($model)
    {
        $namespace = new \Nette\PhpGenerator\PhpNamespace($this->getNamespace().'\Model\ResourceModel\\'.$model);
        $class = $namespace->addClass('Collection');
        $class->setExtends('\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection');
        $method = $class->addMethod('_construct')
          ->addComment('Initialize '.$model.' resource collection')
          ->setVisibility('protected')
          ->setBody('$this->_init('.PHP_EOL.
            '   '.'\\'.$this->getNamespace().'\\Model'.'\\'.$model.'::class,'.PHP_EOL.
            '   '.'\\'.$this->getNamespace().'\\Model\ResourceModel'.'\\'.$model.'::class'.PHP_EOL.
            ');');
        $this->writeFile('generated/Model/ResourceModel/'.$model.'/Collection.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genAdminControllers($entity)
    {
        $this->genAdminAbstractController($entity);
        $this->genAdminViewController($entity);

        //$namespace = new \Nette\PhpGenerator\PhpNamespace($module.'\Controller\Adminhtml\\'.$entity);
        //$this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Edit.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
        //$this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Save.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
        //$this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Delete.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genAdminViewController($entity)
    {
        $namespace = new \Nette\PhpGenerator\PhpNamespace($this->getNamespace().'\Controller\Adminhtml\\'.$entity);
        $class = $namespace->addClass('Index');
        $class
            ->setExtends($this->getNamespace().'\Controller\Adminhtml\\'.$entity)
        ;
        $class->addProperty('resultPageFactory')
              ->setVisibility('protected')
              ->addComment('@var \Magento\Framework\View\Result\PageFactory')
        ;

        $method = $class->addMethod('__construct')
          ->addComment('@param \Magento\Framework\View\Result\PageFactory $resultPageFactory')
          ->addComment('@param \Magento\Framework\Registry $coreRegistry')
          ->addComment('@param \Magento\Backend\App\Action\Context $context')
          ->setBody('$this->resultPageFactory = $resultPageFactory;'.PHP_EOL
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
            .'//$dataPersistor = $this->_objectManager->get(\Magento\Framework\App\Request\DataPersistorInterface::class);'.PHP_EOL
            .'//$dataPersistor->clear(\''.$this->module.'_'.strtolower($entity).'\');'.PHP_EOL
            .'return $resultPage;');
        //echo $namespace;
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'/Index.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }

    protected function genAdminAbstractController($entity)
    {
        $namespace = new \Nette\PhpGenerator\PhpNamespace($this->getNamespace().'\Controller\Adminhtml');
        $class = $namespace->addClass($entity);
        $class
            ->setAbstract()
            ->setExtends('\Magento\Backend\App\Action')
        ;
        $class->addConstant('ADMIN_RESOURCE', $this->getFullname().'::'.strtolower($entity))
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
          ->setBody('parent::__construct($context);'.PHP_EOL.'$this->_coreRegistry = $coreRegistry;');

        $method->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $method->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');


        $method = $class->addMethod('initPage')
          ->setVisibility('protected')
          ->addComment('@param \Magento\Backend\Model\View\Result\Page $resultPage')
          ->addComment('@return \Magento\Backend\Model\View\Result\Page')
          ->setBody('$resultPage->setActiveMenu(\''.$this->getFullname().'::'.strtolower($entity).'\');'.PHP_EOL
          .'//->addBreadcrumb(__(\''.$entity.'\'), __(\''.$entity.'\'));'.PHP_EOL
          . 'return $resultPage;');
        $method->addParameter('resultPage');
        $this->writeFile('generated/Controller/Adminhtml/'.$entity.'.php', '<?php'.PHP_EOL.PHP_EOL.$namespace);
    }


    protected function genAdminRoute($path)
    {
        $xml = $this->getXmlManager()->generateAdminRoute($this->module, $path, $this->getFullname());
        $this->writeFile('generated/etc/adminhtml/routes.xml', $xml);
    }

    protected function genAdminLayouts($entity)
    {
        $uiComponent = $this->module.'_'.$entity.'_listing';
        $uiComponent = 'cms_block_listing';
        $xml = $this->getXmlManager()->generateAdminLayoutIndex($uiComponent);
        $path = $this->module.'_'.$entity.'_index';
        $this->writeFile('generated/view/adminhtml/layout/'.$path.'.xml', $xml);

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
