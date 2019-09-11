<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;

class Banner extends AbstractModel implements \Mygento\SampleModule\Api\Data\BannerInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mygento\SampleModule\Model\ResourceModel\Banner::class);
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
     * Get name
     * @return string|null
     */
    public function getName()
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get subname
     * @return string|null
     */
    public function getSubname()
    {
        return $this->getData(self::SUBNAME);
    }

    /**
     * Set subname
     * @param string $subname
     * @return $this
     */
    public function setSubname($subname)
    {
        return $this->setData(self::SUBNAME, $subname);
    }

    /**
     * Get family
     * @return string|null
     */
    public function getFamily()
    {
        return $this->getData(self::FAMILY);
    }

    /**
     * Set family
     * @param string $family
     * @return $this
     */
    public function setFamily($family)
    {
        return $this->setData(self::FAMILY, $family);
    }

    /**
     * Get is active
     * @return bool|null
     */
    public function getIsActive()
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set is active
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive)
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get product id
     * @return int|null
     */
    public function getProductId()
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }
}
