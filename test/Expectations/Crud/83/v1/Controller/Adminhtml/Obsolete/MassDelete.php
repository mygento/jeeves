<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Ui\Component\MassAction\Filter;
use Mygento\SampleModule\Api\ObsoleteRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Obsolete;
use Mygento\SampleModule\Model\ResourceModel\Obsolete\CollectionFactory;

class MassDelete extends Obsolete
{
    private Filter $filter;
    private CollectionFactory $collectionFactory;

    public function __construct(
        CollectionFactory $collectionFactory,
        Filter $filter,
        ObsoleteRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
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
