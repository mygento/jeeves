<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

interface CustomerAddressInterface extends IdentityInterface
{
    public const CACHE_TAG = 'sam_c';
    public const ID = 'id';
    public const CITY = 'city';
    public const CUSTOMER_GROUP = 'customer_group';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DISCOUNT = 'discount';
    public const PRICE = 'price';

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
     * @return string
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
     * @return int
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
     * @return string
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
     * @return string
     */
    public function getUpdatedAt();

    /**
     * Set updated at
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt);

    /**
     * Get discount
     * @return float|null
     */
    public function getDiscount();

    /**
     * Set discount
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount);

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
