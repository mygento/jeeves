<?php

namespace Mygento\Samplemodule\Api\Data;

interface CustomeraddressInterface
{
    const ID = 'id';
    const CITY = 'city';
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
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
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
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setCity($city);

    /**
     * Get created at
     * @return string|null
     */
    public function getCreatedAt();

    /**
     * Set created at
     * @param string $createdAt
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
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
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
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
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setPrice($price);
}
