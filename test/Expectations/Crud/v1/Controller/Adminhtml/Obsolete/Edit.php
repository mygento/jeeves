<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Mygento\SampleModule\Controller\Adminhtml\Obsolete
{
    /** @var \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory */
    private $entityFactory;

    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /**
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory $entityFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory $entityFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Obsolete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $entityId = $this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();
        if ($entityId) {
            try {
                $entity = $this->repository->getById($entityId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Obsolete no longer exists'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_module_obsolete', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mygento_SampleModule::obsolete');
        $resultPage->addBreadcrumb(
            $entityId ? __('Edit Obsolete') : __('New Obsolete'),
            $entityId ? __('Edit Obsolete') : __('New Obsolete')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Obsolete'));
        $resultPage->getConfig()->getTitle()->prepend(
            $entity->getId() ? $entity->getTitle() : __('New Obsolete')
        );

        return $resultPage;
    }
}
