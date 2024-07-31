<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Card;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Mygento\SampleModule\Api\CardRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Card;
use Mygento\SampleModule\Model\ResourceModel\Card\CollectionFactory;

class MassDelete extends Card
{
    public function __construct(
        private readonly CollectionFactory $collectionFactory,
        private readonly Filter $filter,
        CardRepositoryInterface $repository,
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
