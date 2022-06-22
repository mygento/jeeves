<?php

namespace Mygento\Jeeves\Model;

class DbTable
{
    private $name;
    private $columns;
    private $indexes;
    private $fk;
    private $resource;
    private $engine;
    private $comment;
    private $primary;

    public function __construct(
        string $name,
        array $columns,
        string $comment,
        //        array $constraints = [],
        array $indexes = [],
        array $fk = [],
        array $primary = [],
        string $resource = 'default',
        string $engine = 'innodb'
    ) {
        $this->columns = $columns;
        $this->resource = $resource;
        $this->comment = $comment;
        $this->engine = $engine;
        $this->name = $name;
        $this->fk = $fk;
        $this->indexes = $indexes;
        $this->primary = $primary;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getColumns(): array
    {
        return $this->columns ?? [];
    }

    public function getResource(): string
    {
        return $this->resource;
    }

    public function getEngine(): string
    {
        return $this->engine;
    }

    public function getComment(): string
    {
        return $this->comment;
    }

    public function getIndexes(): array
    {
        return $this->indexes;
    }

    public function getFk(): array
    {
        return $this->fk;
    }

    public function getPrimary(): array
    {
        return $this->primary;
    }
}
