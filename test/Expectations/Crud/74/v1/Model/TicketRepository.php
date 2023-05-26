<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mygento\SampleModule\Api\Data\TicketInterface;
use Mygento\SampleModule\Api\Data\TicketInterfaceFactory;
use Mygento\SampleModule\Api\Data\TicketSearchResultsInterface;
use Mygento\SampleModule\Api\Data\TicketSearchResultsInterfaceFactory;
use Mygento\SampleModule\Api\TicketRepositoryInterface;
use Mygento\SampleModule\Model\ResourceModel\Ticket\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class TicketRepository implements TicketRepositoryInterface
{
    private ResourceModel\Ticket $resource;
    private CollectionFactory $collectionFactory;
    private TicketInterfaceFactory $entityFactory;
    private TicketSearchResultsInterfaceFactory $searchResultsFactory;
    private CollectionProcessorInterface $collectionProcessor;

    public function __construct(
        ResourceModel\Ticket $resource,
        CollectionFactory $collectionFactory,
        TicketInterfaceFactory $entityFactory,
        TicketSearchResultsInterfaceFactory $searchResultsFactory,
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
    public function getById(int $entityId): TicketInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('A Sample Module Ticket with id "%1" does not exist', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(TicketInterface $entity): TicketInterface
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Sample Module Ticket'),
                $exception
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(TicketInterface $entity): bool
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

    public function getList(SearchCriteriaInterface $criteria): TicketSearchResultsInterface
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Ticket\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var TicketSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
