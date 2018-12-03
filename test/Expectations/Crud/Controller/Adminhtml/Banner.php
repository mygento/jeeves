<?php

namespace Mygento\Sample\Controller\Adminhtml;

abstract class Banner extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mygento_Sample::sample_banner';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Banner repository
     *
     * @var \Mygento\Sample\Api\BannerRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Mygento\Sample\Api\BannerRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Sample\Api\BannerRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mygento_Sample::banner');
        //->addBreadcrumb(__('Banner'), __('Banner'));
        return $resultPage;
    }
}
