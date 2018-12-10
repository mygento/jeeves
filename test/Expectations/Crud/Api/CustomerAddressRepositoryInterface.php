<?php

namespace Mygento\SampleModule\Api;

interface CustomerAddressRepositoryInterface
{
    /**
     * Save CustomerAddress
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressInterface
     */
    public function save(Data\CustomerAddressInterface $entity);

    /**
     * Retrieve CustomerAddress
     * @param int $entityId
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressInterface
     */
    public function getById($entityId);

    /**
     * Retrieve CustomerAddress entities matching the specified criteria
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return \Mygento\SampleModule\Api\Data\CustomerAddressSearchResultsInterface
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete CustomerAddress
     * @param \Mygento\SampleModule\Api\Data\CustomerAddressInterface $entity
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function delete(Data\CustomerAddressInterface $entity);

    /**
     * Delete CustomerAddress
     * @param int $entityId
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     * @return bool true on success
     */
    public function deleteById($entityId);
}
