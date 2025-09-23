<?php

namespace Mygento\SampleModule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

/**
 * @api
 */
interface CardRepositoryInterface
{
    /**
     * Save Card
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\CardInterface
     */
    public function save(Data\CardInterface $entity): Data\CardInterface;

    /**
     * Retrieve Card
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\CardInterface
     */
    public function getById(int $entityId): Data\CardInterface;

    /**
     * Retrieve Card entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\CardSearchResultsInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\CardSearchResultsInterface;

    /**
     * Delete Card
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\CardInterface $entity): bool;

    /**
     * Delete Card
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById(int $entityId): bool;
}
