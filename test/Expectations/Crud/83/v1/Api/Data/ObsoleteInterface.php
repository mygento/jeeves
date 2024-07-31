<?php

namespace Mygento\SampleModule\Api\Data;

interface ObsoleteInterface
{
    public const ID = 'id';
    public const NAME = 'name';

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
     * Get name
     */
    public function getName(): ?string;

    /**
     * Set name
     */
    public function setName(?string $name): self;
}
