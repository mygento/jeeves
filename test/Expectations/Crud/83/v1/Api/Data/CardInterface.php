<?php

namespace Mygento\SampleModule\Api\Data;

use Magento\Framework\DataObject\IdentityInterface;

/**
 * @api
 */
interface CardInterface extends IdentityInterface
{
    public const string CACHE_TAG = 'samp_card';
    public const string CARD_ID = 'card_id';
    public const string TITLE = 'title';
    public const string CODE = 'code';
    public const string CATEGORY_ID = 'category_id';
    public const string IS_ACTIVE = 'is_active';
    public const string STORE_ID = 'store_id';

    /**
     * Get card id
     * @return int|null
     */
    public function getCardId(): ?int;

    /**
     * Set card id
     * @return $this
     */
    public function setCardId(?int $cardId): self;

    /**
     * Get title
     * @return string|null
     */
    public function getTitle(): ?string;

    /**
     * Set title
     * @return $this
     */
    public function setTitle(?string $title): self;

    /**
     * Get code
     * @return string|null
     */
    public function getCode(): ?string;

    /**
     * Set code
     * @return $this
     */
    public function setCode(?string $code): self;

    /**
     * Get category id
     * @return int
     */
    public function getCategoryId(): int;

    /**
     * Set category id
     * @return $this
     */
    public function setCategoryId(int $categoryId): self;

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
     * Get store id
     * @return array|null
     */
    public function getStoreId(): ?array;

    /**
     * Set store id
     * @return $this
     */
    public function setStoreId(?array $storeId): self;

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
