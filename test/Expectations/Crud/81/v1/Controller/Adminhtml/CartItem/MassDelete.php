<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CartItem;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Mygento\SampleModule\Api\CartItemRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\CartItem;
use Mygento\SampleModule\Model\ResourceModel\CartItem\CollectionFactory;

class MassDelete extends CartItem
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly Filter $filter,
        CartItemRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Execute action
     */
    public function execute(): ResultInterface
    {
        $collection = $this->filter->getCollection($this->collectionFactory->create());
        $collectionSize = $collection->getSize();

        foreach ($collection as $entity) {
            $this->repository->delete($entity);
        }
        $this->messageManager->addSuccessMessage(
            __('A total of %1 record(s) have been deleted.', $collectionSize)->render()
        );
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);

        return $resultRedirect->setPath('*/*/');
    }
}
