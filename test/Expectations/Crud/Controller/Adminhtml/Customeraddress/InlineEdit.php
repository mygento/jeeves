<?php

namespace Mygento\Sample\Controller\Adminhtml\Customeraddress;

class InlineEdit extends \Mygento\Sample\Controller\Adminhtml\Customeraddress
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    private $jsonFactory;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Mygento\Sample\Api\CustomeraddressRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        $this->jsonFactory = $jsonFactory;
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Execute action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];
        if ($this->getRequest()->getParam('isAjax')) {
            $postItems = $this->getRequest()->getParam('items', []);
            if (!count($postItems)) {
                $messages[] = __('Please correct the data sent.');
                $error = true;
            }
            foreach (array_keys($postItems) as $id) {
                try {
                    $entity = $this->repository->getById($id);
                    $entity->setData(array_merge($entity->getData(), $postItems[$id]));
                    $this->repository->save($entity);
                } catch (\Magento\Framework\Exception\NoSuchEntityException $e) {
                    $messages[] = $id . ' -> ' . __('Not found');
                    $error = true;
                    continue;
                } catch (\Exception $e) {
                    $messages[] = __($e->getMessage());
                    $error = true;
                    continue;
                }
            }
        }
        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error
        ]);
    }
}
