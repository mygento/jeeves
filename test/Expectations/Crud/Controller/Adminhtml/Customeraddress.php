<?php

namespace Mygento\Samplemodule\Controller\Adminhtml;

abstract class Customeraddress extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Mygento_Samplemodule::samplemodule_customeraddress';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Customeraddress repository
     *
     * @var \Mygento\Samplemodule\Api\CustomeraddressRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Mygento\Samplemodule\Api\CustomeraddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\Samplemodule\Api\CustomeraddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);
        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }

    /**
     * Init page
     *
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Mygento_Samplemodule::customeraddress');
        //->addBreadcrumb(__('Customeraddress'), __('Customeraddress'));
        return $resultPage;
    }
}
