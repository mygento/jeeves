<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CustomerAddress;

class NewAction extends \Mygento\SampleModule\Controller\Adminhtml\CustomerAddress
{
    /** @var \Magento\Backend\Model\View\Result\ForwardFactory */
    private $resultForwardFactory;

    /**
     * @param \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory
     * @param \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Backend\Model\View\Result\ForwardFactory $resultForwardFactory,
        \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->resultForwardFactory = $resultForwardFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Create new CustomerAddress
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
