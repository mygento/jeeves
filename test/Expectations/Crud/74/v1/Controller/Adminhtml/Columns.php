<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\ColumnsRepositoryInterface;

abstract class Columns extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::columns';

    protected Registry $coreRegistry;
    protected ColumnsRepositoryInterface $repository;

    public function __construct(ColumnsRepositoryInterface $repository, Registry $coreRegistry, Action\Context $context)
    {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
