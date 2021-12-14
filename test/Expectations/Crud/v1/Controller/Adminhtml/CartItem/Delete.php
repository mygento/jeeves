<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CartItem;

use Magento\Framework\Controller\ResultInterface;
use Mygento\SampleModule\Controller\Adminhtml\CartItem;

class Delete extends CartItem
{
    /**
     * Delete Cart Item action
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityId = $this->getRequest()->getParam('id');
        if (!$entityId) {
            $this->messageManager->addErrorMessage(__('We can not find a Cart Item to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->repository->deleteById($entityId);
            $this->messageManager->addSuccessMessage(__('You deleted the Cart Item'));

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
    }
}
