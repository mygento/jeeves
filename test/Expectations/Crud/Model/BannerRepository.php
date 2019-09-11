<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Data\Collection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class BannerRepository implements \Mygento\SampleModule\Api\BannerRepositoryInterface
{
    /** @var \Mygento\SampleModule\Model\ResourceModel\Banner */
    private $resource;

    /** @var \Mygento\SampleModule\Model\ResourceModel\Banner\CollectionFactory */
    private $collectionFactory;

    /** @var \Mygento\SampleModule\Api\Data\BannerInterfaceFactory */
    private $entityFactory;

    /** @var \Mygento\SampleModule\Api\Data\BannerSearchResultsInterfaceFactory */
    private $searchResultsFactory;

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\Banner $resource
     * @param \Mygento\SampleModule\Model\ResourceModel\Banner\CollectionFactory $collectionFactory
     * @param \Mygento\SampleModule\Api\Data\BannerInterfaceFactory $entityFactory
     * @param \Mygento\SampleModule\Api\Data\BannerSearchResultsInterfaceFactory $searchResultsFactory
     */
    public function __construct(
        ResourceModel\Banner $resource,
        ResourceModel\Banner\CollectionFactory $collectionFactory,
        \Mygento\SampleModule\Api\Data\BannerInterfaceFactory $entityFactory,
        \Mygento\SampleModule\Api\Data\BannerSearchResultsInterfaceFactory $searchResultsFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
    }

    /**
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
     */
    public function getById($entityId)
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException(
                __('Sample Module Banner with id "%1" does not exist.', $entityId)
            );
        }
        return $entity;
    }

    /**
     * @param \Mygento\SampleModule\Api\Data\BannerInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
     */
    public function save(\Mygento\SampleModule\Api\Data\BannerInterface $entity)
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
     * @param \Mygento\SampleModule\Api\Data\BannerInterface $entity
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @return bool
     */
    public function delete(\Mygento\SampleModule\Api\Data\BannerInterface $entity)
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
     * @return \Mygento\SampleModule\Api\Data\BannerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Banner\Collection $collection */
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

        /** @var \Mygento\SampleModule\Api\Data\BannerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }
}
