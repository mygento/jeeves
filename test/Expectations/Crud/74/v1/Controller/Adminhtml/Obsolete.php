<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

abstract class Obsolete extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::obsolete';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * Obsolete repository
     *
     * @var \Mygento\SampleModule\Api\ObsoleteRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
