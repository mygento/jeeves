<?php

namespace Mygento\Sample\Controller\Adminhtml\Customeraddress;

use Magento\Framework\Exception\LocalizedException;

class Save extends \Mygento\Sample\Controller\Adminhtml\Customeraddress
{
    /**
     * @param \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor
     * @param \Mygento\Sample\Model\CustomeraddressFactory $entityFactory
     * @param \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Mygento\Sample\Model\CustomeraddressFactory $entityFactory,
        \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->dataPersistor = $dataPersistor;
        $this->entityFactory = $entityFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Save Customeraddress action
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
            } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                if (!$entity->getId() && $entityId) {
                    $this
                        ->messageManager
                        ->addErrorMessage(__('This Customeraddress no longer exists.'));
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
            $this->messageManager->addSuccessMessage(__('You saved the Customeraddress'));
            $this->dataPersistor->clear('sample_customeraddress');
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $entity->getId()]);
            }
            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Customeraddress'));
        }
        $this->dataPersistor->set('sample_customeraddress', $data);
        return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}
