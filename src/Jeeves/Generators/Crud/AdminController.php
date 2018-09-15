<?php
namespace Mygento\Jeeves\Generators\Crud;

use Nette\PhpGenerator\PhpNamespace;

class AdminController
{
    public function genAdminAbstractController($className, $fullName, $repository, $rootNamespace)
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

        $class->addProperty('repository')
            ->setVisibility('protected')
            ->addComment($className . ' repository')
            ->addComment('')
            ->addComment('@var ' . $repository)
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('parent::__construct($context);' . PHP_EOL
                . '$this->repository = $repository;' . PHP_EOL
                . '$this->coreRegistry = $coreRegistry;');

        $construct->addParameter('repository')->setTypeHint($repository);
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

    public function genAdminViewController($entity, $shortName, $repository, $rootNamespace)
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
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->resultPageFactory = $resultPageFactory;' . PHP_EOL
            . 'parent::__construct($repository, $coreRegistry, $context);
          ');

        $construct->addParameter('resultPageFactory')->setTypeHint('\Magento\Framework\View\Result\PageFactory');
        $construct->addParameter('repository')->setTypeHint($repository);
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

        $class->addProperty('entityFactory')
            ->setVisibility('private')
            ->addComment('@var ' . $entityClass . 'Factory')
        ;
        $class->addProperty('resultPageFactory')
            ->setVisibility('private')
            ->addComment('@var \Magento\Framework\View\Result\PageFactory')
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
            ->addComment('@param \Magento\Framework\View\Result\PageFactory $resultPageFactory')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->entityFactory = $entityFactory;' . PHP_EOL
            . '$this->resultPageFactory = $resultPageFactory;' . PHP_EOL
            . 'parent::__construct($repository, $coreRegistry, $context);
          ');

        $construct->addParameter('entityFactory')->setTypeHint($entityClass . 'Factory');
        $construct->addParameter('resultPageFactory')->setTypeHint('\Magento\Framework\View\Result\PageFactory');
        $construct->addParameter('repository')->setTypeHint($repository);
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
        $namespace->addUse('Magento\Framework\Exception\LocalizedException');
        $class = $namespace->addClass('Save')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor')
            ->addComment('@param ' . $entityClass . 'Factory $entityFactory')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->dataPersistor = $dataPersistor;' . PHP_EOL
            . '$this->entityFactory = $entityFactory;' . PHP_EOL
            . 'parent::__construct($repository, $coreRegistry, $context);
          ');

        $construct->addParameter('dataPersistor')
            ->setTypeHint('\Magento\Framework\App\Request\DataPersistorInterface');
        $construct->addParameter('entityFactory')->setTypeHint($entityClass . 'Factory');
        $construct->addParameter('repository')->setTypeHint($repository);
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

    public function genAdminDeleteController(
        $entity,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass('Delete')
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $class->addMethod('execute')
            ->addComment('Delete ' . $entity . ' action')
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody('/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
                . '$resultRedirect = $this->resultRedirectFactory->create();' . PHP_EOL
                . '$entityId = $this->getRequest()->getParam(\'id\');' . PHP_EOL
                . 'if (!$entityId) {' . PHP_EOL
                . '    $this->messageManager->addErrorMessage(__(\'We can not find a ' . $entity . ' to delete.\'));' . PHP_EOL
                . '     return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '}' . PHP_EOL
                . 'try {' . PHP_EOL
                . '    $this->repository->deleteById($entity);' . PHP_EOL
                . '    $this->messageManager->addSuccessMessage(__(\'You deleted the ' . $entity . '\'));' . PHP_EOL
                . '    return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL
                . '} catch (\Exception $e) {' . PHP_EOL
                . '    $this->messageManager->addErrorMessage($e->getMessage());' . PHP_EOL
                . '}' . PHP_EOL
                . 'return $resultRedirect->setPath(\'*/*/edit\', [\'id\' => $entityId]);');

        return $namespace;
    }

    public function genAdminNewController(
        $entity,
        $className,
        $repository,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $class->addProperty('resultForwardFactory')
            ->setVisibility('private')
            ->addComment('@var \Magento\Backend\Model\View\Result\ForwardFactory')
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->resultForwardFactory = $resultForwardFactory;' . PHP_EOL
            . 'parent::__construct($repository, $coreRegistry, $context);
          ');

        $construct->addParameter('resultForwardFactory')
            ->setTypeHint('\Magento\Backend\Model\View\Result\ForwardFactory');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $class->addMethod('execute')
            ->addComment('Create new ' . $entity)
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody('/** @var \Magento\Framework\Controller\Result\Forward $resultForward */' . PHP_EOL
                . '$resultForward = $this->resultForwardFactory->create();' . PHP_EOL
                . 'return $resultForward->forward(\'edit\');');

        return $namespace;
    }

    public function genAdminInlineController(
        $entity,
        $className,
        $repository,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $class->addProperty('jsonFactory')
            ->setVisibility('private')
            ->addComment('@var \Magento\Framework\Controller\Result\JsonFactory')
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->jsonFactory = $jsonFactory;' . PHP_EOL
            . 'parent::__construct($repository, $coreRegistry, $context);
          ');

        $construct->addParameter('jsonFactory')
            ->setTypeHint('\Magento\Framework\Controller\Result\JsonFactory');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $class->addMethod('execute')
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody('/** @var \Magento\Framework\Controller\Result\Json $resultJson */' . PHP_EOL
        . '$resultJson = $this->jsonFactory->create();' . PHP_EOL
        . '$error = false;' . PHP_EOL
        . '$messages = [];' . PHP_EOL

        . 'if ($this->getRequest()->getParam(\'isAjax\')) {' . PHP_EOL
        . '    $postItems = $this->getRequest()->getParam(\'items\', []);' . PHP_EOL
        . '    if (!count($postItems)) {' . PHP_EOL
        . '        $messages[] = __(\'Please correct the data sent.\');' . PHP_EOL
        . '        $error = true;' . PHP_EOL
        . '    }' . PHP_EOL
        . '    foreach (array_keys($postItems) as $id) {' . PHP_EOL
        . '        try {' . PHP_EOL
        . '            $entity = $this->repository->getById($id);' . PHP_EOL
        . '            $entity->setData(array_merge($entity->getData(), $postItems[$id]));' . PHP_EOL
        . '            $this->repository->save($entity);' . PHP_EOL
        . '        } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {' . PHP_EOL
        . '            $messages[] = $id .\' -> \'. __(\'Not found\');' . PHP_EOL
        . '            $error = true;' . PHP_EOL
        . '            continue;' . PHP_EOL
        . '        } catch (\Exception $e) {' . PHP_EOL
        . '            $messages[] = __($e->getMessage());' . PHP_EOL
        . '            $error = true;' . PHP_EOL
        . '            continue;' . PHP_EOL
        . '        }' . PHP_EOL
        . '    }' . PHP_EOL
        . '}' . PHP_EOL

        . 'return $resultJson->setData([' . PHP_EOL
        . '    \'messages\' => $messages,' . PHP_EOL
        . '    \'error\' => $error' . PHP_EOL
        . ']);' . PHP_EOL);

        return $namespace;
    }

    public function genAdminMassController(
        $entity,
        $className,
        $collection,
        $repository,
        $rootNamespace
    ) {
        $namespace = new PhpNamespace($rootNamespace . '\Controller\Adminhtml\\' . $entity);
        $class = $namespace->addClass($className)
            ->setExtends($rootNamespace . '\Controller\Adminhtml\\' . $entity)
        ;

        $class->addProperty('filter')
            ->setVisibility('private')
            ->addComment('@var \Magento\Ui\Component\MassAction\Filter')
        ;

        $class->addProperty('collectionFactory')
            ->setVisibility('private')
            ->addComment('@var ' . $collection)
        ;

        $construct = $class->addMethod('__construct')
            ->addComment('@param ' . $collection . ' collectionFactory')
            ->addComment('@param \Magento\Ui\Component\MassAction\Filter $filter')
            ->addComment('@param ' . $repository . ' $repository')
            ->addComment('@param \Magento\Framework\Registry $coreRegistry')
            ->addComment('@param \Magento\Backend\App\Action\Context $context')
            ->setBody('$this->filter = $filter;' . PHP_EOL
            . '$this->collectionFactory = $collectionFactory;' . PHP_EOL
            . 'parent::__construct($repository, $coreRegistry, $context);
          ');

        $construct->addParameter('collectionFactory')
            ->setTypeHint($collection);
        $construct->addParameter('filter')
            ->setTypeHint('\Magento\Ui\Component\MassAction\Filter');
        $construct->addParameter('repository')->setTypeHint($repository);
        $construct->addParameter('coreRegistry')->setTypeHint('\Magento\Framework\Registry');
        $construct->addParameter('context')->setTypeHint('\Magento\Backend\App\Action\Context');

        $class->addMethod('execute')
            ->addComment('')
            ->addComment('@return \Magento\Framework\Controller\ResultInterface')
            ->setBody('$collection = $this->filter->getCollection($this->collectionFactory->create());' . PHP_EOL
        . '$collectionSize = $collection->getSize();' . PHP_EOL . PHP_EOL

        . 'foreach ($collection as $entity) {' . PHP_EOL
        . '    $this->repository->delete($entity);' . PHP_EOL
        . '}' . PHP_EOL

        . '$this->messageManager->addSuccessMessage(' . PHP_EOL
        . '    __(\'A total of %1 record(s) have been deleted.\', $collectionSize)' . PHP_EOL
        . ');' . PHP_EOL

        . '/** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */' . PHP_EOL
        . '$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);' . PHP_EOL
        . 'return $resultRedirect->setPath(\'*/*/\');' . PHP_EOL);

        return $namespace;
    }
}
