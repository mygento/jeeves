<?php

namespace Mygento\Sample\Controller\Adminhtml\Customeraddress;

class NewAction extends \Mygento\Sample\Controller\Adminhtml\Customeraddress
{
    /** @var \Magento\Backend\Model\View\Result\ForwardFactory */
    private $resultForwardFactory;

    /**
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Create new Customeraddress
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
