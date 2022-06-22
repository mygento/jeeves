<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mygento\SampleModule\Api\CartItemRepositoryInterface;
use Mygento\SampleModule\Api\Data\CartItemInterface;
use Mygento\SampleModule\Api\Data\CartItemInterfaceFactory;
use Mygento\SampleModule\Api\Data\CartItemSearchResultsInterface;
use Mygento\SampleModule\Api\Data\CartItemSearchResultsInterfaceFactory;
use Mygento\SampleModule\Model\ResourceModel\CartItem\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CartItemRepository implements CartItemRepositoryInterface
{
    private ResourceModel\CartItem $resource;
    private CollectionFactory $collectionFactory;
    private CartItemInterfaceFactory $entityFactory;
    private CartItemSearchResultsInterfaceFactory $searchResultsFactory;
    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ResourceModel\CartItem $resource,
        CollectionFactory $collectionFactory,
        CartItemInterfaceFactory $entityFactory,
        CartItemSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): CartItemInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('A Sample Module Cart Item with id "%1" does not exist', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(CartItemInterface $entity): CartItemInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Sample Module Cart Item'),
                $exception
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(CartItemInterface $entity): bool
    {
        try {
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(
                __($exception->getMessage())
            );
        }

        return true;
    }

    /**
     * @throws NoSuchEntityException
     * @throws CouldNotDeleteException
     */
    public function deleteById(int $entityId): bool
    {
        return $this->delete($this->getById($entityId));
    }

    public function getList(SearchCriteriaInterface $criteria): CartItemSearchResultsInterface
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\CartItem\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var CartItemSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
