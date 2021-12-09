<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Banner;

class Index extends \Mygento\SampleModule\Controller\Adminhtml\Banner
{
    /** @var \Magento\Framework\View\Result\PageFactory */
    private $resultPageFactory;

    /** @var \Magento\Framework\App\Request\DataPersistorInterface */
    private $dataPersistor;

    /**
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Mygento\SampleModule\Api\BannerRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Mygento\SampleModule\Api\BannerRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->resultPageFactory = $resultPageFactory;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Index action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage
            ->setActiveMenu('Mygento_SampleModule::banner')
            ->getConfig()
            ->getTitle()->prepend(__('Banner'));

        $this->dataPersistor->clear('sample_module_banner');

        return $resultPage;
    }
}
