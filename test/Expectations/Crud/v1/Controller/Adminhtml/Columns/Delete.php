<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Columns;

use Magento\Framework\Controller\ResultInterface;
use Mygento\SampleModule\Controller\Adminhtml\Columns;

class Delete extends Columns
{
    /**
     * Delete Columns action
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityId = (int) $this->getRequest()->getParam('id');
        if (!$entityId) {
            $this->messageManager->addErrorMessage(
                __('We can not find a Columns to delete.')->render()
            );

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->repository->deleteById($entityId);
            $this->messageManager->addSuccessMessage(
                __('You deleted the Columns')->render()
            );

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
    }
}
