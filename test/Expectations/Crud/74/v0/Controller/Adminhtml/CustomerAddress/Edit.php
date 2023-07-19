<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CustomerAddress;

use Magento\Framework\Exception\NoSuchEntityException;

class Edit extends \Mygento\SampleModule\Controller\Adminhtml\CustomerAddress
{
    /** @var \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory */
    private $entityFactory;

    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /**
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory $entityFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory $entityFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Customer Address action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $entityId = (int) $this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();
        if ($entityId) {
            try {
                $entity = $this->repository->getById($entityId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(
                    __('This Customer Address no longer exists')->render()
                );
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_module_customeraddress', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mygento_SampleModule::customeraddress');
        $resultPage->addBreadcrumb(
            $entityId ? __('Edit Customer Address')->render() : __('New Customer Address')->render(),
            $entityId ? __('Edit Customer Address')->render() : __('New Customer Address')->render()
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customer Address')->render());
        $resultPage->getConfig()->getTitle()->prepend(
            $entityId ? $entity->getTitle() : __('New Customer Address')->render()
        );

        return $resultPage;
    }
}
