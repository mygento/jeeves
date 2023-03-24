<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Mygento\SampleModule\Api\CardRepositoryInterface;
use Mygento\SampleModule\Api\Data\CardInterface;
use Mygento\SampleModule\Api\Data\CardInterfaceFactory;
use Mygento\SampleModule\Api\Data\CardSearchResultsInterface;
use Mygento\SampleModule\Api\Data\CardSearchResultsInterfaceFactory;
use Mygento\SampleModule\Model\ResourceModel\Card\CollectionFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class CardRepository implements CardRepositoryInterface
{
    public function __construct(
        private readonly ResourceModel\Card $resource,
        private readonly CollectionFactory $collectionFactory,
        private readonly CardInterfaceFactory $entityFactory,
        private readonly CardSearchResultsInterfaceFactory $searchResultsFactory,
        private readonly StoreManagerInterface $storeManager,
        private readonly CollectionProcessorInterface $collectionProcessor,
    ) {
    }

    /**
     * @throws NoSuchEntityException
     */
    public function getById(int $entityId): CardInterface
    {
        $entity = $this->entityFactory->create();
        $this->resource->load($entity, $entityId);
        if (!$entity->getId()) {
            throw new NoSuchEntityException(
                __('A Sample Module Card with id "%1" does not exist', $entityId)
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotSaveException
     */
    public function save(CardInterface $entity): CardInterface
    {
        if (empty($entity->getStoreId())) {
            $entity->setStoreId([$this->storeManager->getStore()->getId()]);
        }

        try {
            $this->resource->save($entity);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(
                __('Could not save the Sample Module Card'),
                $exception
            );
        }

        return $entity;
    }

    /**
     * @throws CouldNotDeleteException
     */
    public function delete(CardInterface $entity): bool
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

    public function getList(SearchCriteriaInterface $criteria): CardSearchResultsInterface
    {
        /** @var \Mygento\SampleModule\Model\ResourceModel\Card\Collection $collection */
        $collection = $this->collectionFactory->create();

        $this->collectionProcessor->process($criteria, $collection);

        /** @var CardSearchResultsInterface $searchResults */
        $searchResults = $this->searchResultsFactory->create();
        $searchResults->setSearchCriteria($criteria);
        $searchResults->setItems($collection->getItems());
        $searchResults->setTotalCount($collection->getSize());

        return $searchResults;
    }
}
