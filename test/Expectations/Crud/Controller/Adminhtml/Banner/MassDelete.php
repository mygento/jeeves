<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Banner;

class MassDelete extends \Mygento\SampleModule\Controller\Adminhtml\Banner
{
    /** @var \Magento\Ui\Component\MassAction\Filter */
    private $filter;

    /** @var \Mygento\SampleModule\Model\ResourceModel\Banner\CollectionFactory */
    private $collectionFactory;

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\Banner\CollectionFactory $collectionFactory
     * @param \Magento\Ui\Component\MassAction\Filter $filter
     * @param \Mygento\SampleModule\Api\BannerRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\SampleModule\Model\ResourceModel\Banner\CollectionFactory $collectionFactory,
        \Magento\Ui\Component\MassAction\Filter $filter,
        \Mygento\SampleModule\Api\BannerRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $entity) {
            $this->repository->delete($entity);
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collectionSize)
        );
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        return $resultRedirect->setPath('*/*/');
    }
}
