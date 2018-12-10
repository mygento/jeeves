<?php

namespace Mygento\Samplemodule\Model;

use Magento\Framework\Model\AbstractModel;

class Customeraddress extends AbstractModel implements \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mygento\Samplemodule\Model\ResourceModel\Customeraddress::class);
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
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ID, $id);
    }

    /**
     * Get city
     * @return string|null
     */
    public function getCity()
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     * @param string $city
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setCity($city)
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get created at
     * @return string|null
     */
    public function getCreatedAt()
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     * @param string $createdAt
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setCreatedAt($createdAt)
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at
     * @return string|null
     */
    public function getUpdatedAt()
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     * @param string $updatedAt
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setUpdatedAt($updatedAt)
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
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
     * @return \Mygento\Samplemodule\Api\Data\CustomeraddressInterface
     */
    public function setPrice($price)
    {
        return $this->setData(self::PRICE, $price);
    }
}
