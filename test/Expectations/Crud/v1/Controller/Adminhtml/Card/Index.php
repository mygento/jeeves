<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Card;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\CardRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Card;

class Index extends Card
{
    private PageFactory $resultPageFactory;

    private DataPersistorInterface $dataPersistor;

    public function __construct(
        PageFactory $resultPageFactory,
        DataPersistorInterface $dataPersistor,
        CardRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Index action
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Mygento_SampleModule::card')
            ->getConfig()
            ->getTitle()->prepend(__('Card')->render());

        $this->dataPersistor->clear('sample_module_card');

        return $resultPage;
    }
}
