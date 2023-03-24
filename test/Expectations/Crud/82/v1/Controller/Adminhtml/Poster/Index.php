<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Poster;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\PageFactory;
use Mygento\SampleModule\Api\PosterRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Poster;

class Index extends Poster
{
    public function __construct(
        private readonly PageFactory $resultPageFactory,
        private readonly DataPersistorInterface $dataPersistor,
        PosterRepositoryInterface $repository,
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
            ->setActiveMenu('Mygento_SampleModule::poster')
            ->getConfig()
            ->getTitle()->prepend(__('Poster')->render());

        $this->dataPersistor->clear('sample_module_poster');

        return $resultPage;
    }
}
