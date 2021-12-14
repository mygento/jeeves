<?php

namespace Mygento\SampleModule\Api;

use Magento\Framework\Api\SearchCriteriaInterface;

interface PosterRepositoryInterface
{
    /**
     * Save Poster
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(Data\PosterInterface $entity): Data\PosterInterface;

    /**
     * Retrieve Poster
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById(int $entityId): Data\PosterInterface;

    /**
     * Retrieve Poster entities matching the specified criteria
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(SearchCriteriaInterface $searchCriteria): Data\PosterSearchResultsInterface;

    /**
     * Delete Poster
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function delete(Data\PosterInterface $entity): bool;

    /**
     * Delete Poster
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById(int $entityId): bool;
}
