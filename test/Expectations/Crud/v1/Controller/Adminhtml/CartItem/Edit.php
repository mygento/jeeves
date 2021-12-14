<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CartItem;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\CartItemRepositoryInterface;
use Mygento\SampleModule\Api\Data\CartItemInterfaceFactory;
use Mygento\SampleModule\Controller\Adminhtml\CartItem;

class Edit extends CartItem
{
    private CartItemInterfaceFactory $entityFactory;

    private PageFactory $resultPageFactory;

    public function __construct(
        CartItemInterfaceFactory $entityFactory,
        PageFactory $resultPageFactory,
        CartItemRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Cart Item action
     */
    public function execute(): ResultInterface
    {
        $entityId = $this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();
        if ($entityId) {
            try {
                $entity = $this->repository->getById($entityId);
            } catch (NoSuchEntityException $e) {
                $this->messageManager->addErrorMessage(__('This Cart Item no longer exists'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_module_cartitem', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mygento_SampleModule::cartitem');
        $resultPage->addBreadcrumb(
            $entityId ? __('Edit Cart Item') : __('New Cart Item'),
            $entityId ? __('Edit Cart Item') : __('New Cart Item')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Cart Item'));
        $resultPage->getConfig()->getTitle()->prepend(
            $entity->getId() ? $entity->getTitle() : __('New Cart Item')
        );

        return $resultPage;
    }
}
