<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Card;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\CardRepositoryInterface;
use Mygento\SampleModule\Api\Data\CardInterfaceFactory;
use Mygento\SampleModule\Controller\Adminhtml\Card;

class Edit extends Card
{
    private CardInterfaceFactory $entityFactory;
    private PageFactory $resultPageFactory;

    public function __construct(
        CardInterfaceFactory $entityFactory,
        PageFactory $resultPageFactory,
        CardRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
    }

    /**
     * Edit Card action
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
                    __('This Card no longer exists')->render()
                );
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();

                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_module_card', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mygento_SampleModule::card');
        $resultPage->addBreadcrumb(
            $entityId ? __('Edit Card')->render() : __('New Card')->render(),
            $entityId ? __('Edit Card')->render() : __('New Card')->render()
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Card')->render());
        $resultPage->getConfig()->getTitle()->prepend(
            $entityId ? $entity->getTitle() : __('New Card')->render()
        );

        return $resultPage;
    }
}
