<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

interface CardInterface extends IdentityInterface
{
    public const CACHE_TAG = 'samp_card';
    public const CARD_ID = 'card_id';
    public const TITLE = 'title';
    public const CODE = 'code';
    public const CATEGORY_ID = 'category_id';
    public const IS_ACTIVE = 'is_active';
    public const STORE_ID = 'store_id';

    /**
     * Get card id
     */
    public function getCardId(): ?int;

    /**
     * Set card id
     */
    public function setCardId(?int $cardId): self;

    /**
     * Get title
     */
    public function getTitle(): ?string;

    /**
     * Set title
     */
    public function setTitle(?string $title): self;

    /**
     * Get code
     */
    public function getCode(): ?string;

    /**
     * Set code
     */
    public function setCode(?string $code): self;

    /**
     * Get category id
     */
    public function getCategoryId(): int;

    /**
     * Set category id
     */
    public function setCategoryId(int $categoryId): self;

    /**
     * Get is active
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Get store id
     */
    public function getStoreId(): ?array;

    /**
     * Set store id
     */
    public function setStoreId(?array $storeId): self;

    /**
     * Get ID
     */
    public function getId(): ?int;

    /**
     * Set ID
     * @param int $id
     */
    public function setId($id): self;
}
