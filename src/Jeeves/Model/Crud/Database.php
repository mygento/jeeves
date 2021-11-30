<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\Model\DbColumn;
use Mygento\Jeeves\Model\Generator;

class Database extends Generator
{
    public function getColumns(Entity $entity): array
    {
        $columns = $entity->getColumns();

        return array_map(function ($column, $param) {
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
                $onUpdate = $param['on_update'];
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
        }, array_keys($columns), $columns);
    }
}
