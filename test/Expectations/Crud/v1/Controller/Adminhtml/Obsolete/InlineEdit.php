<?php

namespace Mygento\SampleModule\Controller\Adminhtml\Obsolete;

use Magento\Framework\Exception\NoSuchEntityException;

class InlineEdit extends \Mygento\SampleModule\Controller\Adminhtml\Obsolete
{
    /** @var \Magento\Framework\Controller\Result\JsonFactory */
    private $jsonFactory;

    /**
     * @param \Magento\Framework\Controller\Result\JsonFactory $jsonFactory
     * @param \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository
     * @param \Magento\Framework\Registry $coreRegistry
     * @param \Magento\Backend\App\Action\Context $context
     */
    public function __construct(
        \Magento\Framework\Controller\Result\JsonFactory $jsonFactory,
        \Mygento\SampleModule\Api\ObsoleteRepositoryInterface $repository,
        \Magento\Framework\Registry $coreRegistry,
        \Magento\Backend\App\Action\Context $context
    ) {
        parent::__construct($repository, $coreRegistry, $context);

        $this->jsonFactory = $jsonFactory;
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
