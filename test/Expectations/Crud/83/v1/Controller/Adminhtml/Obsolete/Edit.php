<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory;
use Mygento\SampleModule\Api\ObsoleteRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Obsolete;

class Edit extends Obsolete
{
    private ObsoleteInterfaceFactory $entityFactory;
    private PageFactory $resultPageFactory;

    public function __construct(
        ObsoleteInterfaceFactory $entityFactory,
        PageFactory $resultPageFactory,
        ObsoleteRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Obsolete action
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
                    __('This Obsolete no longer exists')->render()
                );
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
            $entityId ? __('Edit Obsolete')->render() : __('New Obsolete')->render(),
            $entityId ? __('Edit Obsolete')->render() : __('New Obsolete')->render()
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Obsolete')->render());
        $resultPage->getConfig()->getTitle()->prepend(
            $entityId ? $entity->getTitle() : __('New Obsolete')->render()
        );

        return $resultPage;
    }
}
