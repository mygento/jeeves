<?php

namespace Mygento\SampleModule\Api\Data;

interface TicketInterface
{
    public const string TICKET_ID = 'ticket_id';
    public const string NAME = 'name';
    public const string IS_ACTIVE = 'is_active';

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
     * Is active
     */
    public function isActive(): bool;

    /**
     * Set active
     */
    public function setActive(bool $isActive): self;

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
