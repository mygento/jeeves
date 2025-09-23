<?php

namespace Mygento\SampleModule\Api\Data;

interface ColumnsInterface
{
    public const string ID = 'id';
    public const string IS_ACTIVE = 'is_active';
    public const string HAS_FLAG = 'has_flag';
    public const string MERGE_DATE = 'merge_date';
    public const string DISCOUNT = 'discount';
    public const string COST = 'cost';
    public const string PRICE = 'price';
    public const string NAME = 'name';
    public const string DESCRIPTION = 'description';
    public const string CREATED_AT = 'created_at';
    public const string UPDATED_AT = 'updated_at';

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
     * Is active
     */
    public function isActive(): bool;

    /**
     * Set active
     */
    public function setActive(bool $isActive): self;

    /**
     * Has flag
     */
    public function hasFlag(): ?bool;

    /**
     * Set has flag
     */
    public function setHasFlag(?bool $hasFlag): self;

    /**
     * Get merge date
     */
    public function getMergeDate(): ?string;

    /**
     * Set merge date
     */
    public function setMergeDate(?string $mergeDate): self;

    /**
     * Get discount
     */
    public function getDiscount(): float;

    /**
     * Set discount
     */
    public function setDiscount(float $discount): self;

    /**
     * Get cost
     */
    public function getCost(): ?float;

    /**
     * Set cost
     */
    public function setCost(?float $cost): self;

    /**
     * Get price
     */
    public function getPrice(): ?float;

    /**
     * Set price
     */
    public function setPrice(?float $price): self;

    /**
     * Get name
     */
    public function getName(): string;

    /**
     * Set name
     */
    public function setName(string $name): self;

    /**
     * Get description
     */
    public function getDescription(): string;

    /**
     * Set description
     */
    public function setDescription(string $description): self;

    /**
     * Get created at
     */
    public function getCreatedAt(): string;

    /**
     * Set created at
     */
    public function setCreatedAt(string $createdAt): self;

    /**
     * Get updated at
     */
    public function getUpdatedAt(): string;

    /**
     * Set updated at
     */
    public function setUpdatedAt(string $updatedAt): self;
}
