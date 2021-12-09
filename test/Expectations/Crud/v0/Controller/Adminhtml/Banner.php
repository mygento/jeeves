<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

abstract class Banner extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::banner';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Banner repository
     *
     * @var \Mygento\SampleModule\Api\BannerRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Mygento\SampleModule\Api\BannerRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\SampleModule\Api\BannerRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
