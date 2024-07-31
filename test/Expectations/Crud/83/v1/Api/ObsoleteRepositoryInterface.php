<?php

namespace Mygento\SampleModule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface ObsoleteRepositoryInterface
{
    /**
     * Save Obsolete
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\ObsoleteInterface $entity): Data\ObsoleteInterface;

    /**
     * Retrieve Obsolete
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\ObsoleteInterface;

    /**
     * Retrieve Obsolete entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\ObsoleteSearchResultsInterface;

    /**
     * Delete Obsolete
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\ObsoleteInterface $entity): bool;

    /**
     * Delete Obsolete
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;
}
