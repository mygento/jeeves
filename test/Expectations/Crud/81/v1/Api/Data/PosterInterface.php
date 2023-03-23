<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @api
 */
interface PosterInterface extends IdentityInterface
{
    public const CACHE_TAG = 'samp_poster';
    public const ID = 'id';
    public const NAME = 'name';
    public const SUBNAME = 'subname';
    public const FAMILY = 'family';
    public const IS_ACTIVE = 'is_active';
    public const PRODUCT_ID = 'product_id';

    /**
     * Get id
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set id
     * @param int $id
     * @return $this
     */
    public function setId($id): self;

    /**
     * Get name
     * @return string|null
     */
    public function getName(): ?string;

    /**
     * Set name
     * @return $this
     */
    public function setName(?string $name): self;

    /**
     * Get subname
     * @return string|null
     */
    public function getSubname(): ?string;

    /**
     * Set subname
     * @return $this
     */
    public function setSubname(?string $subname): self;

    /**
     * Get family
     * @return string|null
     */
    public function getFamily(): ?string;

    /**
     * Set family
     * @return $this
     */
    public function setFamily(?string $family): self;

    /**
     * Get is active
     * @return bool
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     * @return $this
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Get product id
     * @return int|null
     */
    public function getProductId(): ?int;

    /**
     * Set product id
     * @return $this
     */
    public function setProductId(?int $productId): self;
}
