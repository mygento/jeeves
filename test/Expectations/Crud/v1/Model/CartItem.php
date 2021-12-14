<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\CartItemInterface;

class CartItem extends AbstractModel implements CartItemInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_samplemodule_cart_item';

    /**
     * Get cart id
     */
    public function getCartId(): ?int
    {
        return $this->getData(self::CART_ID);
    }

    /**
     * Set cart id
     */
    public function setCartId(?int $cartId): self
    {
        return $this->setData(self::CART_ID, $cartId);
    }

    /**
     * Get is active
     */
    public function getIsActive(): bool
    {
        return $this->getData(self::IS_ACTIVE);
    }

    /**
     * Set is active
     */
    public function setIsActive(bool $isActive): self
    {
        return $this->setData(self::IS_ACTIVE, $isActive);
    }

    /**
     * Get delivery date
     */
    public function getDeliveryDate(): ?string
    {
        return $this->getData(self::DELIVERY_DATE);
    }

    /**
     * Set delivery date
     */
    public function setDeliveryDate(?string $deliveryDate): self
    {
        return $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    /**
     * Get city
     */
    public function getCity(): string
    {
        return $this->getData(self::CITY);
    }

    /**
     * Set city
     */
    public function setCity(string $city): self
    {
        return $this->setData(self::CITY, $city);
    }

    /**
     * Get customer group
     */
    public function getCustomerGroup(): int
    {
        return $this->getData(self::CUSTOMER_GROUP);
    }

    /**
     * Set customer group
     */
    public function setCustomerGroup(int $customerGroup): self
    {
        return $this->setData(self::CUSTOMER_GROUP, $customerGroup);
    }

    /**
     * Get description
     */
    public function getDescription(): string
    {
        return $this->getData(self::DESCRIPTION);
    }

    /**
     * Set description
     */
    public function setDescription(string $description): self
    {
        return $this->setData(self::DESCRIPTION, $description);
    }

    /**
     * Get created at
     */
    public function getCreatedAt(): string
    {
        return $this->getData(self::CREATED_AT);
    }

    /**
     * Set created at
     */
    public function setCreatedAt(string $createdAt): self
    {
        return $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * Get updated at
     */
    public function getUpdatedAt(): string
    {
        return $this->getData(self::UPDATED_AT);
    }

    /**
     * Set updated at
     */
    public function setUpdatedAt(string $updatedAt): self
    {
        return $this->setData(self::UPDATED_AT, $updatedAt);
    }

    /**
     * Get discount
     */
    public function getDiscount(): ?float
    {
        return $this->getData(self::DISCOUNT);
    }

    /**
     * Set discount
     */
    public function setDiscount(?float $discount): self
    {
        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * Get price
     */
    public function getPrice(): ?float
    {
        return $this->getData(self::PRICE);
    }

    /**
     * Set price
     */
    public function setPrice(?float $price): self
    {
        return $this->setData(self::PRICE, $price);
    }

    /**
     * Get ID
     */
    public function getId(): ?int
    {
        return $this->getData(self::CART_ID);
    }

    /**
     * Set ID
     */
    public function setId(?int $id): self
    {
        return $this->setData(self::CART_ID, $id);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\CartItem::class);
    }
}
