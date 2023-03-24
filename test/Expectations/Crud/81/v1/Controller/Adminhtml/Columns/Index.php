<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Columns;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\ColumnsRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Columns;

class Index extends Columns
{
    public function __construct(
        private readonly PageFactory $resultPageFactory,
        private readonly DataPersistorInterface $dataPersistor,
        ColumnsRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Index action
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Mygento_SampleModule::columns')
            ->getConfig()
            ->getTitle()->prepend(__('Columns')->render());

        $this->dataPersistor->clear('sample_module_columns');

        return $resultPage;
    }
}
