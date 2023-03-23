<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

class Delete extends \Mygento\SampleModule\Controller\Adminhtml\Obsolete
{
    /**
     * Delete Obsolete action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        $entityId = (int) $this->getRequest()->getParam('id');
        if (!$entityId) {
            $this->messageManager->addErrorMessage(
                __('We can not find a Obsolete to delete.')->render()
            );

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $this->repository->deleteById($entityId);
            $this->messageManager->addSuccessMessage(
                __('You deleted the Obsolete')->render()
            );

            return $resultRedirect->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $resultRedirect->setPath('*/*/edit', ['id' => $entityId]);
    }
}
