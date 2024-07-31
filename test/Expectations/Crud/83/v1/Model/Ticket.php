<?php

namespace Mygento\SampleModule\Model;

use Magento\Framework\Model\AbstractModel;
use Mygento\SampleModule\Api\Data\TicketInterface;

class Ticket extends AbstractModel implements TicketInterface
{
    /** @inheritDoc */
    protected $_eventPrefix = 'mygento_samplemodule_ticket';

    /**
     * Get ticket id
     */
    public function getTicketId(): ?int
    {
        return $this->getData(self::TICKET_ID);
    }

    /**
     * Set ticket id
     */
    public function setTicketId(?int $ticketId): self
    {
        return $this->setData(self::TICKET_ID, $ticketId);
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
     * Get ID
     */
    public function getId(): ?int
    {
        return $this->getData(self::TICKET_ID);
    }

    /**
     * Set ID
     * @param int $id
     */
    public function setId($id): self
    {
        return $this->setData(self::TICKET_ID, $id);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\Ticket::class);
    }
}
