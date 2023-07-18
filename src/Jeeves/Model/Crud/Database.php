<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Model\DbColumn;
use Mygento\Jeeves\Model\Generator;

class Database extends Generator
{
    public function getColumns(Entity $entity): array
    {
        $columns = $entity->getColumns();

        return array_map([$this, 'mapColumn'], array_keys($columns), $columns);
    }

    public function getColumnsPerStore(?DbColumn $column): array
    {
        return [
            new DbColumn(
                'entity_id',
                $column !== null ? $column->getType() : 'int',
                false,
                'Entity ID',
                false,
                $column !== null ? $column->getUnsigned() : true
            ),
            new DbColumn(
                'store_id',
                'smallint',
                false,
                'Store ID',
                false,
                true
            ),
        ];
    }

    public function getIndexesPerStore(Entity $entity): array
    {
        return [
            'IX_' . strtoupper($entity->getName()) . '_STORE_ID' => [
                'columns' => [
                    'store_id',
                ],
            ],
        ];
    }

    public function getFkPerStore(Entity $entity): array
    {
        return [
            'FK_' . strtoupper($entity->getName()) . '_STORE_ID' => [
                'column' => 'store_id',
                'referenceTable' => 'store',
                'referenceColumn' => 'store_id',
            ],
            'FK_' . strtoupper($entity->getName()) . '_ENT_ID' => [
                'column' => 'entity_id',
                'referenceTable' => $entity->getTablename(),
                'referenceColumn' => $entity->getPrimaryKey(),
                'indexName' => 'IX_ENT_ID',
            ],
        ];
    }

    public function getPrimaryPerStore(): array
    {
        return [
            'entity_id',
            'store_id',
        ];
    }

    public function findPrimary(Entity $entity): ?DbColumn
    {
        $columns = $entity->getColumns();
        foreach ($columns as $n => $c) {
            if (isset($c['pk']) && true == $c['pk']) {
                return $this->mapColumn($n, $c);
            }
        }

        return null;
    }

    private function mapColumn(string $column, array $param): DbColumn
    {
        $nullable = $param['nullable'] ?? true;
        $identity = null;
        $unsigned = null;
        $precision = null;
        $scale = null;
        $length = null;
        $default = null;
        $onUpdate = null;

        switch ($param['type']) {
            case 'boolean':
                $nullable = false;
                break;
            case 'blob':
            case 'date':
            case 'datetime':
            case 'timestamp':
            case 'varbinary':
                break;
            case 'int':
            case 'smallint':
            case 'bigint':
            case 'tinyint':
                $identity = $param['identity'] ?? false;
                $unsigned = $param['unsigned'] ?? false;
                break;
            case 'price':
            case 'real':
            case 'decimal':
            case 'float':
            case 'double':
                $precision = $param['precision'] ?? 10;
                $scale = $param['scale'] ?? 4;
                break;
            case 'text':
            case 'mediumtext':
            case 'longtext':
                break;
            case 'varchar':
                $length = $param['length'] ?? 255;
                break;
            default:
                throw new \Exception('Error column type');
        }
        $columnType = $param['type'];
        if ($param['type'] === 'price') {
            $columnType = 'decimal';
        }
        if (isset($param['default'])) {
            $default = (string) $param['default'];
        }
        if (isset($param['on_update'])) {
            $onUpdate = (bool) $param['on_update'];
        }

        if (isset($param['pk']) && true == $param['pk']) {
            $nullable = false;
        }

        return new DbColumn(
            $column,
            $columnType,
            $nullable,
            $param['comment'] ?? ucfirst($column),
            $identity,
            $unsigned,
            $length,
            $precision,
            $scale,
            $default,
            $onUpdate
        );
    }
}
