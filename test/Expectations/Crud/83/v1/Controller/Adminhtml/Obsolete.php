<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\ObsoleteRepositoryInterface;

abstract class Obsolete extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::obsolete';

    protected Registry $coreRegistry;
    protected ObsoleteRepositoryInterface $repository;

    public function __construct(ObsoleteRepositoryInterface $repository, Registry $coreRegistry, Action\Context $context)
    {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
