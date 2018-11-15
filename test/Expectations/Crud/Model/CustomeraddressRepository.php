<?php

namespace Mygento\Sample\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;

class CustomeraddressRepository implements \Mygento\Sample\Api\CustomeraddressRepositoryInterface
{
    /** @var \Mygento\Sample\Model\ResourceModel\Customeraddress */
    private $resource;

    /** @var \Mygento\Sample\Model\ResourceModel\Customeraddress\CollectionFactory */
    private $collectionFactory;

    /** @var \Mygento\Sample\Model\CustomeraddressFactory */
    private $entityFactory;

    /** @var \Mygento\Sample\Api\Data\CustomeraddressSearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /**
     * @param \Mygento\Sample\Model\ResourceModel\Customeraddress $resource
     * @param \Mygento\Sample\Model\ResourceModel\Customeraddress\CollectionFactory $collectionFactory
     * @param \Mygento\Sample\Model\CustomeraddressFactory $entityFactory
     * @param \Mygento\Sample\Api\Data\CustomeraddressSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceModel\Customeraddress $resource,
        ResourceModel\Customeraddress\CollectionFactory $collectionFactory,
        CustomeraddressFactory $entityFactory,
        \Mygento\Sample\Api\Data\CustomeraddressSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Mygento\Sample\Model\Customeraddress
     */
    public function getById($entityId)
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Sample Customeraddress with id "%1" does not exist.', $entityId)
            );
        }
        return $entity;
    }

    /**
     * @param \Mygento\Sample\Api\Data\CustomeraddressInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Mygento\Sample\Model\Customeraddress
     */
    public function save(\Mygento\Sample\Api\Data\CustomeraddressInterface $entity)
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
     * @param \Mygento\Sample\Api\Data\CustomeraddressInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Mygento\Sample\Api\Data\CustomeraddressInterface $entity)
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
     * @return \Mygento\Sample\Api\Data\CustomeraddressSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mygento\Sample\Model\ResourceModel\Customeraddress\Collection $collection */
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

        /** @var \Mygento\Sample\Api\Data\CustomeraddressSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
