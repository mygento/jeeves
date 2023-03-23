<?php

namespace Mygento\SampleModule\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\PosterRepositoryInterface;

abstract class Poster extends Action
{
    /**
     * Authorization level
     *
     * @see _isAllowed()
     */
    public const ADMIN_RESOURCE = 'Mygento_SampleModule::poster';

    protected Registry $coreRegistry;
    protected PosterRepositoryInterface $repository;

    public function __construct(PosterRepositoryInterface $repository, Registry $coreRegistry, Action\Context $context)
    {
        parent::__construct($context);

        $this->repository = $repository;
        $this->coreRegistry = $coreRegistry;
    }
}
