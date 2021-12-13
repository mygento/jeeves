<?php

namespace Mygento\SampleModule\Api\Data;

interface ColumnsInterface
{
    public const ID = 'id';
    public const IS_ACTIVE = 'is_active';
    public const HAS_FLAG = 'has_flag';
    public const MERGE_DATE = 'merge_date';
    public const DISCOUNT = 'discount';
    public const COST = 'cost';
    public const PRICE = 'price';
    public const NAME = 'name';
    public const DESCRIPTION = 'description';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';

    /**
     * Get id
     */
    public function getId(): ?int;

    /**
     * Set id
     */
    public function setId(?int $id): self;

    /**
     * Get is active
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Get has flag
     */
    public function getHasFlag(): ?bool;

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
