<?php

namespace Mygento\SampleModule\Api\Data;

interface BannerInterface
{
    const ID = 'id';
    const NAME = 'name';
    const SUBNAME = 'subname';
    const PRODUCT_ID = 'product_id';

    /**
     * Get id
     * @return int|null
     */
    public function getId();

    /**
     * Set id
     * @param int $id
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
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
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
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
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
     */
    public function setSubname($subname);

    /**
     * Get product id
     * @return int|null
     */
    public function getProductId();

    /**
     * Set product id
     * @param int $productId
     * @return \Mygento\SampleModule\Api\Data\BannerInterface
     */
    public function setProductId($productId);
}
