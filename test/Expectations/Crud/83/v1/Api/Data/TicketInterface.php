<?php

namespace Mygento\SampleModule\Api\Data;

interface TicketInterface
{
    public const TICKET_ID = 'ticket_id';
    public const NAME = 'name';
    public const IS_ACTIVE = 'is_active';

    /**
     * Get ticket id
     */
    public function getTicketId(): ?int;

    /**
     * Set ticket id
     */
    public function setTicketId(?int $ticketId): self;

    /**
     * Get name
     */
    public function getName(): ?string;

    /**
     * Set name
     */
    public function setName(?string $name): self;

    /**
     * Get is active
     */
    public function getIsActive(): bool;

    /**
     * Set is active
     */
    public function setIsActive(bool $isActive): self;

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
