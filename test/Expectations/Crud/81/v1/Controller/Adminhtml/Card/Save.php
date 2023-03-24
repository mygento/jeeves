<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Card;

use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\CardRepositoryInterface;
use Mygento\SampleModule\Api\Data\CardInterfaceFactory;
use Mygento\SampleModule\Controller\Adminhtml\Card;

class Save extends Card
{
    public function __construct(
        private readonly DataPersistorInterface $dataPersistor,
        private readonly CardInterfaceFactory $entityFactory,
        CardRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Save Card action
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
        $entityId = (int) $this->getRequest()->getParam('id');
        $entity = $this->entityFactory->create();
        if ($entityId) {
            try {
                $entity = $this->repository->getById($entityId);
            } catch (NoSuchEntityException $e) {
                if (!$entity->getId()) {
                    $this->messageManager->addErrorMessage(
                        __('This Card no longer exists')->render()
                    );

                    return $resultRedirect->setPath('*/*/');
                }
            }
        }
        if (empty($data['card_id'])) {
            $data['card_id'] = null;
        }
        $entity->setData($data);

        try {
            $this->repository->save($entity);
            $this->messageManager->addSuccessMessage(
                __('You saved the Card')->render()
            );
            $this->dataPersistor->clear('sample_module_card');
            if ($this->getRequest()->getParam('back')) {
                return $resultRedirect->setPath('*/*/edit', ['id' => $entity->getId()]);
            }

            return $resultRedirect->setPath('*/*/');
        } catch (LocalizedException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        } catch (\Exception $e) {
            $this->messageManager->addExceptionMessage(
                $e,
                __('Something went wrong while saving the Card')->render()
            );
        }
        $this->dataPersistor->set('sample_module_card', $data);

        return $resultRedirect->setPath('*/*/edit', ['id' => $this->getRequest()->getParam('id')]);
    }
}
