<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\CardInterface;

class Card extends AbstractModel implements CardInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_samplemodule_card';

    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    /**
     * Get card id
     */
    public function getCardId(): ?int
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * Set card id
     */
    public function setCardId(?int $cardId): self
    {
        return $this->setData(self::CARD_ID, $cardId);
    }

    /**
     * Get title
     */
    public function getTitle(): ?string
    {
        return $this->getData(self::TITLE);
    }

    /**
     * Set title
     */
    public function setTitle(?string $title): self
    {
        return $this->setData(self::TITLE, $title);
    }

    /**
     * Get code
     */
    public function getCode(): ?string
    {
        return $this->getData(self::CODE);
    }

    /**
     * Set code
     */
    public function setCode(?string $code): self
    {
        return $this->setData(self::CODE, $code);
    }

    /**
     * Get category id
     */
    public function getCategoryId(): int
    {
        return $this->getData(self::CATEGORY_ID);
    }

    /**
     * Set category id
     */
    public function setCategoryId(int $categoryId): self
    {
        return $this->setData(self::CATEGORY_ID, $categoryId);
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
     * Get store id
     */
    public function getStoreId(): ?array
    {
        return $this->getData(self::STORE_ID);
    }

    /**
     * Set store id
     */
    public function setStoreId(?array $storeId): self
    {
        return $this->setData(self::STORE_ID, $storeId);
    }

    /**
     * Get ID
     */
    public function getId(): ?int
    {
        return $this->getData(self::CARD_ID);
    }

    /**
     * Set ID
     * @param int $id
     */
    public function setId($id): self
    {
        return $this->setData(self::CARD_ID, $id);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Card::class);
    }
}
