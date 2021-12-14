<?php

namespace Mygento\SampleModule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface CartItemRepositoryInterface
{
    /**
     * Save Cart Item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\CartItemInterface $entity): Data\CartItemInterface;

    /**
     * Retrieve Cart Item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\CartItemInterface;

    /**
     * Retrieve Cart Item entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\CartItemSearchResultsInterface;

    /**
     * Delete Cart Item
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\CartItemInterface $entity): bool;

    /**
     * Delete Cart Item
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;
}
