<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

interface BannerInterface extends IdentityInterface
{
    public const CACHE_TAG = 'samp_ban';
    public const ID = 'id';
    public const NAME = 'name';
    public const SUBNAME = 'subname';
    public const FAMILY = 'family';
    public const IS_ACTIVE = 'is_active';
    public const PRODUCT_ID = 'product_id';
    public const STORE_ID = 'store_id';

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
     * Get name
     * @return string|null
     */
    public function getName();

    /**
     * Set name
     * @param string $name
     * @return $this
     */
    public function setName($name);

    /**
     * Get subname
     * @return string|null
     */
    public function getSubname();

    /**
     * Set subname
     * @param string $subname
     * @return $this
     */
    public function setSubname($subname);

    /**
     * Get family
     * @return string|null
     */
    public function getFamily();

    /**
     * Set family
     * @param string $family
     * @return $this
     */
    public function setFamily($family);

    /**
     * Get is active
     * @return bool
     */
    public function getIsActive();

    /**
     * Set is active
     * @param bool $isActive
     * @return $this
     */
    public function setIsActive($isActive);

    /**
     * Get product id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product id
     * @param int $productId
     * @return $this
     */
    public function setProductId($productId);

    /**
     * Get store id
     * @return array|null
     */
    public function getStoreId();

    /**
     * Set store id
     * @param array $storeId
     * @return $this
     */
    public function setStoreId($storeId);
}
