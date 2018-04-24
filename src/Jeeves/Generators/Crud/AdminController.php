<?php
namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class AdminController
{
    public function genAdminAbstractController($className, $fullName, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml');
        $class = $namespace->addClass($className)
            ->setAbstract()
            ->setExtends('\Magento\Backend\App\Action')
        ;
        $class->addConstant('ADMIN_RESOURCE', $fullName . '::' . strtolower($className))
            ->addComment('Authorization level')
            ->addComment('')
            ->addComment('@see _isAllowed()')
        ;

        $class->addProperty('coreRegistry')
            ->setVisibility('protected')
            ->addComment('Core registry')
            ->addComment('')
            ->addComment('@var \Magento\Framework\Registry')
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('parent::__construct($context);' . PHP_EOL . '$this->_coreRegistry = $coreRegistry;');

        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $init = $class->addMethod('initPage')
            ->setVisibility('protected')
            ->addComment('@param \Magento\Backend\Model\View\Result\Page $resultPage')
            ->addComment('@return \Magento\Backend\Model\View\Result\Page')
            ->setBody('$resultPage->setActiveMenu(\'' . $fullName . '::' . strtolower($className) . '\');' . PHP_EOL
            . '//->addBreadcrumb(__(\'' . $className . '\'), __(\'' . $className . '\'));' . PHP_EOL
            . 'return $resultPage;');
        $init->addParameter('resultPage');
        return $namespace;
    }

    public function genAdminViewController($entity, $shortName, $rootNamespace)
    {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass('Index')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;
        $class->addProperty('resultPageFactory')
            ->setVisibility('private')
            ->addComment('@var \Magento\Framework\View\Result\PageFactory')
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\View\Result\PageFactory $resultPageFactory')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->resultPageFactory = $resultPageFactory;' . PHP_EOL
            . 'parent::__construct($coreRegistry, $context);
          ');

        $construct->addParameter('resultPageFactory')->setTypeHint('\Magento\Framework\View\Result\PageFactory');
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $class->addMethod('execute')
            ->addComment('Index action')
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody(' /** @var \Magento\Backend\Model\View\Result\Page $resultPage */' . PHP_EOL
                . '$resultPage = $this->resultPageFactory->create();' . PHP_EOL
                . '$this->initPage($resultPage)->getConfig()->getTitle()->prepend(__(\'' . $entity . '\'));' . PHP_EOL . PHP_EOL
                . '//$dataPersistor = $this->_objectManager->get(\Magento\Framework\App\Request\DataPersistorInterface::class);' . PHP_EOL
                . '//$dataPersistor->clear(\'' . $shortName . '\');' . PHP_EOL
                . 'return $resultPage;');
        return $namespace;
    }

    public function genAdminEditController(
        $entity,
        $shortName,
        $repository,
        $entityClass,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass('Edit')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $class->addProperty('repository')
            ->setVisibility('private')
            ->addComment('@var ' . $repository)
        ;
        $class->addProperty('entityFactory')
            ->setVisibility('private')
            ->addComment('@var ' . $entityClass . 'Factory')
        ;
        $class->addProperty('resultPageFactory')
            ->setVisibility('private')
            ->addComment('@var \Magento\Framework\View\Result\PageFactory')
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
            ->addComment('@param \Magento\Framework\View\Result\PageFactory $resultPageFactory')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->repository = $repository;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            . '$this->resultPageFactory = $resultPageFactory;' . PHP_EOL
            . 'parent::__construct($coreRegistry, $context);
          ');

        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('entityFactory')->setTypeHint($entityClass);
        $construct->addParameter('resultPageFactory')->setTypeHint('\Magento\Framework\View\Result\PageFactory');
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $class->addMethod('execute')
            ->addComment('Edit ' . $entity . ' action')
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody('$entityId = $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . '$entity = $this->entityFactory->create();' . PHP_EOL
                . 'if ($entityId) {' . PHP_EOL
                . 'try {' . PHP_EOL
                . '$entity = $this->repository->getById($entityId);' . PHP_EOL
                . '} catch (\Magento\Framework\Exception\NoSuchEntityException $e) {' . PHP_EOL
                . '$this->messageManager->addError(__(\'This ' . $entity . ' no longer exists.\'));' . PHP_EOL
                . '/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
                . '$resultRedirect = $this->resultRedirectFactory->create();' . PHP_EOL
                . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '}' . PHP_EOL
                . '}' . PHP_EOL
                . '$this->coreRegistry->register(\'' . $shortName . '\', $entity);' . PHP_EOL . PHP_EOL
                . '/** @var \Magento\Backend\Model\View\Result\Page $resultPage */' . PHP_EOL
                . '$resultPage = $this->resultPageFactory->create();' . PHP_EOL
                . '$this->initPage($resultPage)->addBreadcrumb(' . PHP_EOL
                . '    $entityId ? __(\'Edit ' . $entity . '\') : __(\'New ' . $entity . '\'),' . PHP_EOL
                . '    $entityId ? __(\'Edit ' . $entity . '\') : __(\'New ' . $entity . '\')' . PHP_EOL
                . ');' . PHP_EOL
                . '$resultPage->getConfig()->getTitle()->prepend(__(\'' . $entity . '\'));' . PHP_EOL
                . '$resultPage->getConfig()->getTitle()->prepend(' . PHP_EOL
                . '    $entity->getId() ? $entity->getTitle() : __(\'New ' . $entity . '\')' . PHP_EOL
                . ');' . PHP_EOL
                . 'return $resultPage;');

        return $namespace;
    }

    public function genAdminSaveController(
        $entity,
        $shortName,
        $repository,
        $entityClass,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass('Edit')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->dataPersistor = $dataPersistor;' . PHP_EOL
            . '$this->repository = $repository;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            . 'parent::__construct($coreRegistry, $context);
          ');

        $construct->addParameter('dataPersistor')
            ->setTypeHint('\Magento\Framework\App\Request\DataPersistorInterface');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('entityFactory')->setTypeHint($entityClass);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $class->addMethod('execute')
            ->addComment('Save ' . $entity . ' action')
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody('/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
                . '$resultRedirect = $this->resultRedirectFactory->create();' . PHP_EOL
                . '$data = $this->getRequest()->getPostValue();' . PHP_EOL
                . 'if (!$data) {' . PHP_EOL
                . '    return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '}' . PHP_EOL
                . '$entityId = $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . '$entity = $this->entityFactory->create();' . PHP_EOL
                . 'if ($entityId) {' . PHP_EOL
                . '    try {' . PHP_EOL
                . '        $entity = $this->repository->getById($entityId);' . PHP_EOL
                . '    } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {' . PHP_EOL
                . '        if (!$entity->getId() && $entityId) {' . PHP_EOL
                . '            $this' . PHP_EOL
                . '                ->messageManager' . PHP_EOL
                . '                ->addErrorMessage(__(\'This ' . $entity . ' no longer exists.\'));' . PHP_EOL
                . '            return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '        }' . PHP_EOL
                . '    }' . PHP_EOL
                . '}' . PHP_EOL
                . 'if (empty($data[\'id\'])) {' . PHP_EOL
                . '    $data[\'id\'] = null;' . PHP_EOL
                . '}' . PHP_EOL
                . '$entity->setData($data);' . PHP_EOL
                . 'try {' . PHP_EOL
                . '    $this->repository->save($entity);' . PHP_EOL
                . '    $this->messageManager->addSuccessMessage(__(\'You saved the ' . $entity . '\'));' . PHP_EOL
                . '    $this->dataPersistor->clear(\'' . $shortName . '\');' . PHP_EOL
                . '    if ($this->getRequest()->getParam(\'back\')) {' . PHP_EOL
                . '        return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $entity->getId()]);' . PHP_EOL
                . '    }' . PHP_EOL
                . '    return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '} catch (LocalizedException $e) {' . PHP_EOL
                . '    $this->messageManager->addErrorMessage($e->getMessage());' . PHP_EOL
                . '} catch (\Exception $e) {' . PHP_EOL
                . '    $this->messageManager->addExceptionMessage($e, __(\'Something went wrong while saving the ' . $entity . '\'));' . PHP_EOL
                . '}' . PHP_EOL
                . '$this->dataPersistor->set(\'' . $shortName . '\', $data);' . PHP_EOL
                . 'return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $this->getRequest()->getParam(\'id\')]);');

        return $namespace;
    }
}
