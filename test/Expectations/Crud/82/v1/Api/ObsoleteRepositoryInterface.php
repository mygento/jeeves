<?php

namespace Mygento\SampleModule\Api;

interface ObsoleteRepositoryInterface
{
    /**
     * Save Obsolete
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\ObsoleteInterface
     */
    public function save(Data\ObsoleteInterface $entity);

    /**
     * Retrieve Obsolete
     * @param int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\ObsoleteInterface
     */
    public function getById($entityId);

    /**
     * Retrieve Obsolete entities matching the specified criteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\ObsoleteSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete Obsolete
     * @param \Mygento\SampleModule\Api\Data\ObsoleteInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\ObsoleteInterface $entity);

    /**
     * Delete Obsolete
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($entityId);
}
