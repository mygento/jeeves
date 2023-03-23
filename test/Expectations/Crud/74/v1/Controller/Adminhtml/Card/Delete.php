<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Card;

use Magento\Framework\Controller\ResultInterface;
use Mygento\SampleModule\Controller\Adminhtml\Card;

class Delete extends Card
{
    /**
     * Delete Card action
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityId = (int) $this->getRequest()->getParam('id');
        if (!$entityId) {
            $this->messageManager->addErrorMessage(
                __('We can not find a Card to delete.')->render()
            );

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->repository->deleteById($entityId);
            $this->messageManager->addSuccessMessage(
                __('You deleted the Card')->render()
            );

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
    }
}
