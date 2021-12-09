<?php

namespace Mygento\SampleModule\Api;

interface BannerRepositoryInterface
{
    /**
     * Save Banner
     * @param \Mygento\SampleModule\Api\Data\BannerInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
     */
    public function save(Data\BannerInterface $entity);

    /**
     * Retrieve Banner
     * @param int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
     */
    public function getById($entityId);

    /**
     * Retrieve Banner entities matching the specified criteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\BannerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Banner
     * @param \Mygento\SampleModule\Api\Data\BannerInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\BannerInterface $entity);

    /**
     * Delete Banner
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($entityId);
}
