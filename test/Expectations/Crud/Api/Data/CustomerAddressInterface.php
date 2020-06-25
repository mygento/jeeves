<?php

namespace Mygento\SampleModule\Api\Data;

interface CustomerAddressInterface extends \Magento\Framework\DataObject\IdentityInterface
{
    const CACHE_TAG = 'sam_c';
    const ID = 'id';
    const CITY = 'city';
    const CUSTOMER_GROUP = 'customer_group';
    const CREATED_AT = 'created_at';
    const UPDATED_AT = 'updated_at';
    const PRICE = 'price';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $id
     * @return $this
     */
    public function setId($id);

    /**
     * Get city
     * @return string|null
     */
    public function getCity();

    /**
     * Set city
     * @param string $city
     * @return $this
     */
    public function setCity($city);

    /**
     * Get customer group
     * @return int|null
     */
    public function getCustomerGroup();

    /**
     * Set customer group
     * @param int $customerGroup
     * @return $this
     */
    public function setCustomerGroup($customerGroup);

    /**
     * Get created at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * Get updated at
     * @return string|null
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get price
     * @return float|null
     */
    public function getPrice();

    /**
     * Set price
     * @param float $price
     * @return $this
     */
    public function setPrice($price);
}
