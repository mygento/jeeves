<?php

namespace Mygento\Samplemodule\Model;

use Magento\Framework\Model\AbstractModel;

class Banner extends AbstractModel implements \Mygento\Samplemodule\Api\Data\BannerInterface
{
    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Mygento\Samplemodule\Model\ResourceModel\Banner::class);
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
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface
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
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface
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
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface
     */
    public function setSubname($subname)
    {
        return $this->setData(self::SUBNAME, $subname);
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
     * @return \Mygento\Samplemodule\Api\Data\BannerInterface
     */
    public function setProductId($productId)
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }
}
