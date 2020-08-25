<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CustomerAddressRepository implements \Mygento\SampleModule\Api\CustomerAddressRepositoryInterface
{
    /** @var \Mygento\SampleModule\Model\ResourceModel\CustomerAddress */
    private $resource;

    /** @var \Mygento\SampleModule\Model\ResourceModel\CustomerAddress\CollectionFactory */
    private $collectionFactory;

    /** @var \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory */
    private $entityFactory;

    /** @var \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\CustomerAddress $resource
     * @param \Mygento\SampleModule\Model\ResourceModel\CustomerAddress\CollectionFactory $collectionFactory
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory $entityFactory
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterfaceFactory $searchResultsFactory
     * @param CollectionProcessorInterface|null $collectionProcessor
     */
    public function __construct(
        ResourceModel\CustomerAddress $resource,
        ResourceModel\CustomerAddress\CollectionFactory $collectionFactory,
        \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory $entityFactory,
        \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterfaceFactory $searchResultsFactory,
        CollectionProcessorInterface $collectionProcessor = null
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
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressInterface
     */
    public function getById($entityId)
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Sample Module Customer Address with id "%1" does not exist.', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressInterface
     */
    public function save(\Mygento\SampleModule\Api\Data\CustomerAddressInterface $entity)
    {
        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotSaveException(
                __($exception->getMessage())
            );
        }

        return $entity;
    }

    /**
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Mygento\SampleModule\Api\Data\CustomerAddressInterface $entity)
    {
        try {
            $this->resource->delete($entity);
        } catch (\Exception $exception) {
            throw new \Magento\Framework\Exception\CouldNotDeleteException(
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
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\CustomerAddress\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
