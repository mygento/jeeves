<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CartItem;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\CartItemRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\CartItem;

class NewAction extends CartItem
{
    public function __construct(
        private readonly ForwardFactory $resultForwardFactory,
        CartItemRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Create new Cart Item
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
