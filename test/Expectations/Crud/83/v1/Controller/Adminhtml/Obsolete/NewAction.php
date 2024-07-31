<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\ObsoleteRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Obsolete;

class NewAction extends Obsolete
{
    private ForwardFactory $resultForwardFactory;

    public function __construct(
        ForwardFactory $resultForwardFactory,
        ObsoleteRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Create new Obsolete
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
