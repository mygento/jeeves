<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

interface PosterInterface extends IdentityInterface
{
    public const CACHE_TAG = 'samp_poster';
    public const ID = 'id';
    public const NAME = 'name';
    public const SUBNAME = 'subname';
    public const FAMILY = 'family';
    public const IS_ACTIVE = 'is_active';
    public const PRODUCT_ID = 'product_id';
    public const STORE_ID = 'store_id';

    /**
     * Get id
     */
    public function getId(): ?int;

    /**
     * Set id
     * @param int $id
     */
    public function setId($id): self;

    /**
     * Get name
     */
    public function getName(): ?string;

    /**
     * Set name
     */
    public function setName(?string $name): self;

    /**
     * Get subname
     */
    public function getSubname(): ?string;

    /**
     * Set subname
     */
    public function setSubname(?string $subname): self;

    /**
     * Get family
     */
    public function getFamily(): ?string;

    /**
     * Set family
     */
    public function setFamily(?string $family): self;

    /**
     * Get is active
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Get product id
     */
    public function getProductId(): ?int;

    /**
     * Set product id
     */
    public function setProductId(?int $productId): self;

    /**
     * Get store id
     */
    public function getStoreId(): ?array;

    /**
     * Set store id
     */
    public function setStoreId(?array $storeId): self;
}
