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

    public function __construct(
        protected readonly CardRepositoryInterface $repository,
        protected readonly Registry $coreRegistry,
        Action\Context $context,
    ) {
        parent::__construct($context);
    }
}
