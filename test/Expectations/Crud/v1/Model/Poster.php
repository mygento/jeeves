<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\PosterInterface;

class Poster extends AbstractModel implements PosterInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_samplemodule_poster';

    public function getIdentities(): array
    {
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

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
     * Get name
     */
    public function getName(): ?string
    {
        return $this->getData(self::NAME);
    }

    /**
     * Set name
     */
    public function setName(?string $name): self
    {
        return $this->setData(self::NAME, $name);
    }

    /**
     * Get subname
     */
    public function getSubname(): ?string
    {
        return $this->getData(self::SUBNAME);
    }

    /**
     * Set subname
     */
    public function setSubname(?string $subname): self
    {
        return $this->setData(self::SUBNAME, $subname);
    }

    /**
     * Get family
     */
    public function getFamily(): ?string
    {
        return $this->getData(self::FAMILY);
    }

    /**
     * Set family
     */
    public function setFamily(?string $family): self
    {
        return $this->setData(self::FAMILY, $family);
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
     * Get product id
     */
    public function getProductId(): ?int
    {
        return $this->getData(self::PRODUCT_ID);
    }

    /**
     * Set product id
     */
    public function setProductId(?int $productId): self
    {
        return $this->setData(self::PRODUCT_ID, $productId);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Poster::class);
    }
}
