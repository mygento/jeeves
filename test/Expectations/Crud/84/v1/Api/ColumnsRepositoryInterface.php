<?php

namespace Mygento\SampleModule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ColumnsRepositoryInterface
{
    /**
     * Save Columns
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\ColumnsInterface $entity): Data\ColumnsInterface;

    /**
     * Retrieve Columns
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\ColumnsInterface;

    /**
     * Retrieve Columns entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\ColumnsSearchResultsInterface;

    /**
     * Delete Columns
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ColumnsInterface $entity): bool;

    /**
     * Delete Columns
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;
}
