<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CartItem;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\CartItemRepositoryInterface;
use Mygento\SampleModule\Api\Data\CartItemInterfaceFactory;
use Mygento\SampleModule\Controller\Adminhtml\CartItem;

class Save extends CartItem
{
    private DataPersistorInterface $dataPersistor;

    private CartItemInterfaceFactory $entityFactory;

    public function __construct(
        DataPersistorInterface $dataPersistor,
        CartItemInterfaceFactory $entityFactory,
        CartItemRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->dataPersistor = $dataPersistor;
        $this->entityFactory = $entityFactory;
    }

    /**
     * Save Cart Item action
     *
     * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    public function execute(): ResultInterface
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
                        ->addErrorMessage(__('This Cart Item no longer exists'));

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
            $this->messageManager->addSuccessMessage(__('You saved the Cart Item'));
            $this->dataPersistor->clear('sample_module_cartitem');
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $entity->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage($e, __('Something went wrong while saving the Cart Item'));
        }
        $this->dataPersistor->set('sample_module_cartitem', $data);

        return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}
