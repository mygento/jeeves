<?php

namespace Mygento\Sample\Controller\Adminhtml\Banner;

class Edit extends \Mygento\Sample\Controller\Adminhtml\Banner
{
    /** @var \Mygento\Sample\Model\BannerFactory */
    private $entityFactory;

    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /**
     * @param \Mygento\Sample\Model\BannerFactory $entityFactory
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Mygento\Sample\Api\BannerRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Sample\Model\BannerFactory $entityFactory,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Mygento\Sample\Api\BannerRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->entityFactory = $entityFactory;
        $this->resultPageFactory = $resultPageFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Edit Banner action
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
                $this->messageManager->addError(__('This Banner no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }
        $this->coreRegistry->register('sample_banner', $entity);

        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $this->initPage($resultPage)->addBreadcrumb(
            $entityId ? __('Edit Banner') : __('New Banner'),
            $entityId ? __('Edit Banner') : __('New Banner')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Banner'));
        $resultPage->getConfig()->getTitle()->prepend(
            $entity->getId() ? $entity->getTitle() : __('New Banner')
        );
        return $resultPage;
    }
}
