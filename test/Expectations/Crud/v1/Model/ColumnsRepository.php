<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mygento\SampleModule\Api\ColumnsRepositoryInterface;
use Mygento\SampleModule\Api\Data\ColumnsInterface;
use Mygento\SampleModule\Api\Data\ColumnsInterfaceFactory;
use Mygento\SampleModule\Api\Data\ColumnsSearchResultsInterface;
use Mygento\SampleModule\Api\Data\ColumnsSearchResultsInterfaceFactory;
use Mygento\SampleModule\Model\ResourceModel\Columns\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ColumnsRepository implements ColumnsRepositoryInterface
{
    private ResourceModel\Columns $resource;

    private CollectionFactory $collectionFactory;

    private ColumnsInterfaceFactory $entityFactory;

    private ColumnsSearchResultsInterfaceFactory $searchResultsFactory;

    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ResourceModel\Columns $resource,
        CollectionFactory $collectionFactory,
        ColumnsInterfaceFactory $entityFactory,
        ColumnsSearchResultsInterfaceFactory $searchResultsFactory,
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
    public function getById(int $entityId): ColumnsInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('Sample Module Columns with id "%1" does not exist.', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(ColumnsInterface $entity): ColumnsInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __($exception->getMessage())
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(ColumnsInterface $entity): bool
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

    public function getList(SearchCriteriaInterface $criteria): ColumnsSearchResultsInterface
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Columns\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var ColumnsSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
