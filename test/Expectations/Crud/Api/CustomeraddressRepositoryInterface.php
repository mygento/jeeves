<?php

namespace Mygento\Sample\Api;

interface CustomeraddressRepositoryInterface
{
    /**
     * Save customeraddress
     * @param \Mygento\Sample\Api\Data\CustomeraddressInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Sample\Api\Data\CustomeraddressInterface
     */
    public function save(Data\CustomeraddressInterface $entity);

    /**
     * Retrieve customeraddress
     * @param int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Sample\Api\Data\CustomeraddressInterface
     */
    public function getById($entityId);

    /**
     * Retrieve customeraddress entities matching the specified criteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\Sample\Api\Data\CustomeraddressSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete customeraddress
     * @param \Mygento\Sample\Api\Data\CustomeraddressInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\CustomeraddressInterface $entity);

    /**
     * Delete customeraddress
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($entityId);
}
