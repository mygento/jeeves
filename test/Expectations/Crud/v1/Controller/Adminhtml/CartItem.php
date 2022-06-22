<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\CartItemRepositoryInterface;

abstract class CartItem extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::cartitem';

    protected Registry $coreRegistry;
    protected CartItemRepositoryInterface $repository;

    public function __construct(CartItemRepositoryInterface $repository, Registry $coreRegistry, Action\Context $context)
    {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
