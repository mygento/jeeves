<?php

namespace Mygento\Sample\Controller\Adminhtml\Customeraddress;

class Edit extends \Mygento\Sample\Controller\Adminhtml\Customeraddress
{
    /** @var \Mygento\Sample\Model\CustomeraddressFactory */
    private $entityFactory;

    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /**
     * @param \Mygento\Sample\Model\CustomeraddressFactory $entityFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Sample\Model\CustomeraddressFactory $entityFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Edit Customeraddress action
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
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                $this->messageManager->addError(__('This Customeraddress no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_customeraddress', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $entityId ? __('Edit Customeraddress') : __('New Customeraddress'),
            $entityId ? __('Edit Customeraddress') : __('New Customeraddress')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Customeraddress'));
        $resultPage->getConfig()->getTitle()->prepend(
            $entity->getId() ? $entity->getTitle() : __('New Customeraddress')
        );
        return $resultPage;
    }
}
