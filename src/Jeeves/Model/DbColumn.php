<?php

namespace Mygento\Jeeves\Model;

class DbColumn
{
    private $onUpdate;

    private $default;

    private $scale;

    private $precision;

    private $type;

    private $name;

    private $nullable;

    private $identity;

    private $unsigned;

    private $comment;

    private $length;

    public function __construct(
        string $name,
        string $type,
        bool $nullable,
        string $comment = '',
        bool $identity = null,
        bool $unsigned = null,
        int $length = null,
        int $precision = null,
        int $scale = null,
        string $default = null,
        string $onUpdate = null
    ) {
        $this->name = $name;
        $this->type = $type;
        $this->nullable = $nullable;
        $this->identity = $identity;
        $this->unsigned = $unsigned;
        $this->comment = $comment;
        $this->length = $length;
        $this->precision = $precision;
        $this->scale = $scale;
        $this->default = $default;
        $this->onUpdate = $onUpdate;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getNullable()
    {
        return $this->nullable;
    }

    public function getIdentity()
    {
        return $this->identity;
    }

    public function getUnsigned()
    {
        return $this->unsigned;
    }

    public function getComment()
    {
        return $this->comment;
    }

    public function getLength()
    {
        return $this->length;
    }

    public function getScale(): ?int
    {
        return $this->scale;
    }

    public function getPrecision(): ?int
    {
        return $this->precision;
    }

    public function getOnUpdate(): ?string
    {
        return $this->onUpdate;
    }

    public function getDefault(): ?string
    {
        return $this->default;
    }
}
