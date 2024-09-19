<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @api
 */
interface PosterInterface extends IdentityInterface
{
    public const CACHE_TAG = 'samp_poster';
    public const ENTITY_ID = 'entity_id';
    public const NAME = 'name';
    public const SUBNAME = 'subname';
    public const FAMILY = 'family';
    public const ACTIVE = 'active';
    public const PRODUCT_ID = 'product_id';

    /**
     * Get entity id
     * @return int|null
     */
    public function getEntityId(): ?int;

    /**
     * Set entity id
     * @param int $entityId
     * @return $this
     */
    public function setEntityId($entityId): self;

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
     * Is active
     * @return bool
     */
    public function isActive(): bool;

    /**
     * Set active
     * @return $this
     */
    public function setActive(bool $active): self;

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

    /**
     * Get ID
     * @return int|null
     */
    public function getId(): ?int;

    /**
     * Set ID
     * @param int $id
     * @return $this
     */
    public function setId($id): self;
}
