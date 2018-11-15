<?php

namespace Mygento\Sample\Controller\Adminhtml\Customeraddress;

class Delete extends \Mygento\Sample\Controller\Adminhtml\Customeraddress
{
    /**
     * Delete Customeraddress action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityId = $this->getRequest()->getParam('id');
        if (!$entityId) {
            $this->messageManager->addErrorMessage(__('We can not find a Customeraddress to delete.'));
            return $resultRedirect->setPath('*/*/');
        }
        try {
            $this->repository->deleteById($entityId);
            $this->messageManager->addSuccessMessage(__('You deleted the Customeraddress'));
            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }
        return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
    }
}
