<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\CardRepositoryInterface;

abstract class Card extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::card';

    protected Registry $coreRegistry;

    protected CardRepositoryInterface $repository;

    public function __construct(CardRepositoryInterface $repository, Registry $coreRegistry, Action\Context $context)
    {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
