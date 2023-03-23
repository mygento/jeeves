<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;

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

    /** @var StoreManagerInterface */
    private $storeManager;

    /** @var CollectionProcessorInterface */
    private $collectionProcessor;

    /**
     * @param \Mygento\SampleModule\Model\ResourceModel\Banner $resource
     * @param \Mygento\SampleModule\Model\ResourceModel\Banner\CollectionFactory $collectionFactory
     * @param \Mygento\SampleModule\Api\Data\BannerInterfaceFactory $entityFactory
     * @param \Mygento\SampleModule\Api\Data\BannerSearchResultsInterfaceFactory $searchResultsFactory
     * @param StoreManagerInterface $storeManager
     * @param CollectionProcessorInterface $collectionProcessor
     */
    public function __construct(
        ResourceModel\Banner $resource,
        ResourceModel\Banner\CollectionFactory $collectionFactory,
        \Mygento\SampleModule\Api\Data\BannerInterfaceFactory $entityFactory,
        \Mygento\SampleModule\Api\Data\BannerSearchResultsInterfaceFactory $searchResultsFactory,
        StoreManagerInterface $storeManager,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
        $this->entityFactory = $entityFactory;
        $this->searchResultsFactory = $searchResultsFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->storeManager = $storeManager;
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
            throw new NoSuchEntityException(
                __('A Sample Module Banner with id "%1" does not exist', $entityId)
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
        if (empty($entity->getStoreId())) {
            $entity->setStoreId([$this->storeManager->getStore()->getId()]);
        }

        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Sample Module Banner'),
                $exception
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
     * @return \Mygento\SampleModule\Api\Data\BannerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $criteria)
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Banner\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var \Mygento\SampleModule\Api\Data\BannerSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
