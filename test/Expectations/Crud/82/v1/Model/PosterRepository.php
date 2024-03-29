<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mygento\SampleModule\Api\Data\PosterInterface;
use Mygento\SampleModule\Api\Data\PosterInterfaceFactory;
use Mygento\SampleModule\Api\Data\PosterSearchResultsInterface;
use Mygento\SampleModule\Api\Data\PosterSearchResultsInterfaceFactory;
use Mygento\SampleModule\Api\PosterRepositoryInterface;
use Mygento\SampleModule\Model\ResourceModel\Poster\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class PosterRepository implements PosterRepositoryInterface
{
    public function __construct(
        private readonly ResourceModel\Poster $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly PosterInterfaceFactory $entityFactory,
        private readonly PosterSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly CollectionProcessorInterface $collectionProcessor,
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): PosterInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('A Sample Module Poster with id "%1" does not exist', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(PosterInterface $entity): PosterInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Sample Module Poster'),
                $exception
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(PosterInterface $entity): bool
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

    public function getList(SearchCriteriaInterface $criteria): PosterSearchResultsInterface
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Poster\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var PosterSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
