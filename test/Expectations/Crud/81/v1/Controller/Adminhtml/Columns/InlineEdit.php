<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Columns;

use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Registry;
use Mygento\SampleModule\Api\ColumnsRepositoryInterface;
use Mygento\SampleModule\Controller\Adminhtml\Columns;

class InlineEdit extends Columns
{
    public function __construct(
        private readonly JsonFactory $jsonFactory,
        ColumnsRepositoryInterface $repository,
        Registry $coreRegistry,
        Context $context,
    ) {
        parent::__construct($repository, $coreRegistry, $context);
    }

    /**
     * Execute action
     */
    public function execute(): ResultInterface
    {
        /** @var \Magento\Framework\Controller\Result\Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);
        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData([
                'messages' => [__('Please correct the data sent.')->render()],
                'error' => true,
            ]);
        }

        foreach (array_keys($postItems) as $id) {
            try {
                $entity = $this->repository->getById($id);
                $entity->setData(array_merge($entity->getData(), $postItems[$id]));
                $this->repository->save($entity);
            } catch (NoSuchEntityException $e) {
                $messages[] = $id . ' -> ' . __('Not found')->render();
                $error = true;
                continue;
            } catch (\Exception $e) {
                $messages[] = __($e->getMessage());
                $error = true;
                continue;
            }
        }

        return $resultJson->setData([
            'messages' => $messages,
            'error' => $error,
        ]);
    }
}
