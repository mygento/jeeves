<?php

namespace Mygento\Samplemodule\Api;

interface BannerRepositoryInterface
{
    /**
     * Save banner
     * @param \Mygento\Samplemodule\Api\Data\BannerInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface
     */
    public function save(Data\BannerInterface $entity);

    /**
     * Retrieve banner
     * @param int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface
     */
    public function getById($entityId);

    /**
     * Retrieve banner entities matching the specified criteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Samplemodule\Api\Data\BannerSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete banner
     * @param \Mygento\Samplemodule\Api\Data\BannerInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\BannerInterface $entity);

    /**
     * Delete banner
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($entityId);
}
