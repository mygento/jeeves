<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\ColumnsInterface;

class Columns extends AbstractModel implements ColumnsInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_samplemodule_columns';

    /**
     * Get id
     */
    public function getId(): ?int
    {
        return $this->getData(self::ID);
    }

    /**
     * Set id
     * @param int $id
     */
    public function setId($id): self
    {
        return $this->setData(self::ID, $id);
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
     * Get has flag
     */
    public function getHasFlag(): ?bool
    {
        return $this->getData(self::HAS_FLAG);
    }

    /**
     * Set has flag
     */
    public function setHasFlag(?bool $hasFlag): self
    {
        return $this->setData(self::HAS_FLAG, $hasFlag);
    }

    /**
     * Get merge date
     */
    public function getMergeDate(): ?string
    {
        return $this->getData(self::MERGE_DATE);
    }

    /**
     * Set merge date
     */
    public function setMergeDate(?string $mergeDate): self
    {
        return $this->setData(self::MERGE_DATE, $mergeDate);
    }

    /**
     * Get discount
     */
    public function getDiscount(): float
    {
        return $this->getData(self::DISCOUNT);
    }

    /**
     * Set discount
     */
    public function setDiscount(float $discount): self
    {
        return $this->setData(self::DISCOUNT, $discount);
    }

    /**
     * Get cost
     */
    public function getCost(): ?float
    {
        return $this->getData(self::COST);
    }

    /**
     * Set cost
     */
    public function setCost(?float $cost): self
    {
        return $this->setData(self::COST, $cost);
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
     * Get name
     */
    public function getName(): string
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     */
    public function setName(string $name): self
    {
        return $this->setData(self::NAME, $name);
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
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Columns::class);
    }
}
