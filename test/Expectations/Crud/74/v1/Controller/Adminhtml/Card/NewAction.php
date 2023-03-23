<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Card;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\CardRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Card;

class NewAction extends Card
{
    private ForwardFactory $resultForwardFactory;

    public function __construct(
        ForwardFactory $resultForwardFactory,
        CardRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->resultForwardFactory = $resultForwardFactory;
    }

    /**
     * Create new Card
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();

        return $resultForward->forward('edit');
    }
}
