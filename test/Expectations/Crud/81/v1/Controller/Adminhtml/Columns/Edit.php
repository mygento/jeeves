<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Columns;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\ColumnsRepositoryInterface;
use Mygento\SampleModule\Api\Data\ColumnsInterfaceFactory;
use Mygento\SampleModule\Controller\Adminhtml\Columns;

class Edit extends Columns
{
    public function __construct(
        private readonly ColumnsInterfaceFactory $entityFactory,
        private readonly PageFactory $resultPageFactory,
        ColumnsRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Edit Columns action
     */
    public function execute(): ResultInterface
    {
        $entityId = (int) $this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();
        if ($entityId) {
            try {
                $entity = $this->repository->getById($entityId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(
                    __('This Columns no longer exists')->render()
                );
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_module_columns', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mygento_SampleModule::columns');
        $resultPage->addBreadcrumb(
            $entityId ? __('Edit Columns')->render() : __('New Columns')->render(),
            $entityId ? __('Edit Columns')->render() : __('New Columns')->render()
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Columns')->render());
        $resultPage->getConfig()->getTitle()->prepend(
            $entity->getId() ? $entity->getTitle() : __('New Columns')->render()
        );

        return $resultPage;
    }
}
