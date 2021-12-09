<?php

namespace Mygento\SampleModule\Controller\Adminhtml\CustomerAddress;

class Delete extends \Mygento\SampleModule\Controller\Adminhtml\CustomerAddress
{
    /**
     * Delete Customer Address action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityId = $this->getRequest()->getParam('id');
        if (!$entityId) {
            $this->messageManager->addErrorMessage(__('We can not find a Customer Address to delete.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->repository->deleteById($entityId);
            $this->messageManager->addSuccessMessage(__('You deleted the Customer Address'));

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
    }
}
