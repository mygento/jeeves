<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

abstract class CustomerAddress extends \Magento\Backend\App\Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::customeraddress';

    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    protected $coreRegistry;

    /**
     * CustomerAddress repository
     *
     * @var \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface
     */
    protected $repository;

    /**
     * @param \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
