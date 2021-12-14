<?php

namespace Mygento\SampleModule\Api\Data;

interface CartItemInterface
{
    public const CART_ID = 'cart_id';
    public const IS_ACTIVE = 'is_active';
    public const DELIVERY_DATE = 'delivery_date';
    public const CITY = 'city';
    public const CUSTOMER_GROUP = 'customer_group';
    public const DESCRIPTION = 'description';
    public const CREATED_AT = 'created_at';
    public const UPDATED_AT = 'updated_at';
    public const DISCOUNT = 'discount';
    public const PRICE = 'price';

    /**
     * Get cart id
     */
    public function getCartId(): ?int;

    /**
     * Set cart id
     */
    public function setCartId(?int $cartId): self;

    /**
     * Get is active
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     */
    public function setIsActive(bool $isActive): self;

    /**
     * Get delivery date
     */
    public function getDeliveryDate(): ?string;

    /**
     * Set delivery date
     */
    public function setDeliveryDate(?string $deliveryDate): self;

    /**
     * Get city
     */
    public function getCity(): string;

    /**
     * Set city
     */
    public function setCity(string $city): self;

    /**
     * Get customer group
     */
    public function getCustomerGroup(): int;

    /**
     * Set customer group
     */
    public function setCustomerGroup(int $customerGroup): self;

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

    /**
     * Get discount
     */
    public function getDiscount(): ?float;

    /**
     * Set discount
     */
    public function setDiscount(?float $discount): self;

    /**
     * Get price
     */
    public function getPrice(): ?float;

    /**
     * Set price
     */
    public function setPrice(?float $price): self;

    /**
     * Get ID
     */
    public function getId(): ?int;

    /**
     * Set ID
     */
    public function setId(?int $id): self;
}
