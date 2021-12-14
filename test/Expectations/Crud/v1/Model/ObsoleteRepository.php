<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class ObsoleteRepository implements \Mygento\SampleModule\Api\ObsoleteRepositoryInterface
{
    /** @var \Mygento\SampleModule\Model\ResourceModel\Obsolete */
    private $resource;

    /** @var \Mygento\SampleModule\Model\ResourceModel\Obsolete\CollectionFactory */
    private $collectionFactory;

    /** @var \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory */
    private $entityFactory;

    /** @var \Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\Obsolete $resource
     * @param \Mygento\SampleModule\Model\ResourceModel\Obsolete\CollectionFactory $collectionFactory
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory $entityFactory
     * @param \Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel\Obsolete $resource,
        ResourceModel\Obsolete\CollectionFactory $collectionFactory,
        \Mygento\SampleModule\Api\Data\ObsoleteInterfaceFactory $entityFactory,
        \Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Mygento\SampleModule\Api\Data\ObsoleteInterface
     */
    public function getById($entityId)
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('Sample Module Obsolete with id "%1" does not exist.', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Mygento\SampleModule\Api\Data\ObsoleteInterface
     */
    public function save(\Mygento\SampleModule\Api\Data\ObsoleteInterface $entity)
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
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Mygento\SampleModule\Api\Data\ObsoleteInterface $entity)
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
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function deleteById($entityId)
    {
        return $this->delete($this->getById($entityId));
    }

    /**
     * @param \Magento\Framework\Api\SearchCriteriaInterface $criteria
     * @return \Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Obsolete\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
