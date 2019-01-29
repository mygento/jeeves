<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;

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

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\CustomerAddress $resource
     * @param \Mygento\SampleModule\Model\ResourceModel\CustomerAddress\CollectionFactory $collectionFactory
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory $entityFactory
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceModel\CustomerAddress $resource,
        ResourceModel\CustomerAddress\CollectionFactory $collectionFactory,
        \Mygento\SampleModule\Api\Data\CustomerAddressInterfaceFactory $entityFactory,
        \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
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
        foreach ($criteria->getFilterGroups() as $filterGroup) {
            $fields = [];
            $conditions = [];
            foreach ($filterGroup->getFilters() as $filter) {
                $condition = $filter->getConditionType() ? $filter->getConditionType() : 'eq';
                $fields[] = $filter->getField();
                $conditions[] = [$condition => $filter->getValue()];
            }
            if ($fields) {
                $collection->addFieldToFilter($fields, $conditions);
            }
        }
        $sortOrders = $criteria->getSortOrders();
        $sortAsc = SortOrder::SORT_ASC;
        $orderAsc = Collection::SORT_ORDER_ASC;
        $orderDesc = Collection::SORT_ORDER_DESC;
        if ($sortOrders) {
            /** @var SortOrder $sortOrder */
            foreach ($sortOrders as $sortOrder) {
                $collection->addOrder(
                    $sortOrder->getField(),
                    ($sortOrder->getDirection() == $sortAsc) ? $orderAsc : $orderDesc
                );
            }
        }
        $collection->setCurPage($criteria->getCurrentPage());
        $collection->setPageSize($criteria->getPageSize());

        /** @var \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
