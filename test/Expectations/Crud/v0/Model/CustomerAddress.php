<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\CustomerAddressInterface;

class CustomerAddress extends AbstractModel implements CustomerAddressInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_samplemodule_customer_address';

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get id
     * @return int|null
     */
    public function getId()
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param int $id
     * @return $this
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get city
     * @return string
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return $this
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get customer group
     * @return int
     */
    public function getCustomerGroup()
    {
        return $this->getData(self::CUSTOMER_GROUP);
    }

    /**
     * Set customer group
     * @param int $customerGroup
     * @return $this
     */
    public function setCustomerGroup($customerGroup)
    {
        return $this->setData(self::CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * Get created at
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     * @param string $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at
     * @return string
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     * @param string $updatedAt
     * @return $this
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get discount
     * @return float|null
     */
    public function getDiscount()
    {
        return $this->getData(self::DISCOUNT);
    }

    /**
     * Set discount
     * @param float $discount
     * @return $this
     */
    public function setDiscount($discount)
    {
        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * Get price
     * @return float|null
     */
    public function getPrice()
    {
        return $this->getData(self::PRICE);
    }

    /**
     * Set price
     * @param float $price
     * @return $this
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CustomerAddress::class);
    }
}
