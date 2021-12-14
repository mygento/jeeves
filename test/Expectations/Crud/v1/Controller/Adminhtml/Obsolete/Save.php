<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

class Save extends \Mygento\SampleModule\Controller\Adminhtml\Obsolete
{
    /** @var \Magento\Framework\App\Request\DataPersistorInterface */
    private $dataPersistor;

    /** @var \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory */
    private $entityFactory;

    /**
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory $entityFactory
     * @param \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory $entityFactory,
        \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->dataPersistor = $dataPersistor;
        $this->entityFactory = $entityFactory;
    }

    /**
     * Save Obsolete action
     *
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $data = $this->getRequest()->getPostValue();
        if (!$data) {
            return $resultRedirect->setPath('*/*/');
        }
        $entityId = $this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();
        if ($entityId) {
            try {
                $entity = $this->repository->getById($entityId);
            } catch (NoSuchEntityException $e) {
                if (!$entity->getId() && $entityId) {
                    $this
                        ->messageManager
                        ->addErrorMessage(__('This Obsolete no longer exists'));

                    return $resultRedirect->setPath('*/*/');
                }
            }
        }
        if (empty($data['id'])) {
            $data['id'] = null;
        }
        $entity->setData($data);

        try {
            $this->repository->save($entity);
            $this->messageManager->addSuccessMessage(__('You saved the Obsolete'));
            $this->dataPersistor->clear('sample_module_obsolete');
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $entity->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Obsolete'));
        }
        $this->dataPersistor->set('sample_module_obsolete', $data);

        return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}
