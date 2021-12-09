<?php

namespace Mygento\Jeeves\Generators\Crud\Configs;

use Mygento\Jeeves\Generators\Crud\Common;
use Mygento\Jeeves\Model\DbColumn;
use Mygento\Jeeves\Model\DbTable;

class DbSchema extends Common
{
    public function generateSchema(array $schema): string
    {
        $entityList = array_map(function (DbTable $entity) {
            $columnList = $this->getColumns($entity);
            $primaryContraint = $this->getPrimary($entity);
            $constraintList = $this->getConstraints($entity);
            $indexList = $this->getIndexes($entity);
            $indexFKList = $this->getIndexFK($entity);

            return [
                'name' => 'table',
                'attributes' => [
                    'name' => $entity->getName(),
                    'resource' => 'default',
                    'engine' => 'innodb',
                    'comment' => $entity->getComment(),
                ],
                'value' => array_merge(
                    $columnList,
                    [$primaryContraint],
                    $indexList,
                    $constraintList,
                    $indexFKList
                ),
            ];
        }, $schema);

        $service = $this->getService();

        return $service->write('schema', function ($writer) use ($entityList) {
            $writer->setIndentString(self::TAB);
            $writer->writeAttribute('xsi:noNamespaceSchemaLocation', 'urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd');
            $writer->write($entityList);
        });
    }

    public function getIndexFK(DbTable $entity): array
    {
        $indexColumn = array_filter(array_map(
            function ($indx) {
                if (count($indx['columns']) > 1) {
                    return null;
                }

                return $indx['columns'][0];
            },
            array_values($entity->getIndexes())
        ));

        return array_filter(array_map(
            function ($param) use ($indexColumn) {
                if (in_array($param['column'], $indexColumn)) {
                    return [];
                }

                return [
                    'name' => 'index',
                    'attributes' => [
                        'indexType' => 'btree',
                        'referenceId' => $param['indexName'],
                    ],
                    'value' => array_map(
                        [$this, 'getIndexColumn'],
                        [$param['column']]
                    ),
                ];
            },
            array_values($entity->getFk())
        ));
    }

    private function getColumns(DbTable $entity): array
    {
        return array_map(function (DbColumn $column) {
            $type = [];
            if ($column->getIdentity() !== null) {
                $type['identity'] = var_export($column->getIdentity(), true);
                $type['unsigned'] = var_export($column->getUnsigned(), true);
            }
            if ($column->getPrecision() !== null) {
                $type['precision'] = var_export($column->getPrecision(), true);
                $type['scale'] = var_export($column->getScale(), true);
            }
            if ($column->getLength() !== null) {
                $type['length'] = var_export($column->getLength(), true);
            }

            $optional = [];
            if ($column->getDefault() !== null) {
                $optional['default'] = (string) $column->getDefault();
            }
            if ($column->getOnUpdate() !== null) {
                $optional['on_update'] = var_export($column->getOnUpdate(), true);
            }

            return [
                'name' => 'column',
                'attributes' => array_merge([
                    'xsi:type' => $column->getType(),
                    'name' => $column->getName(),
                    'nullable' => var_export($column->getNullable(), true),
                ], $type, $optional, ['comment' => $column->getComment()]),
            ];
        }, $entity->getColumns());
    }

    private function getConstraints(DbTable $entity): array
    {
        $fk = $entity->getFk();
        $tablename = $entity->getName();

        return array_map(
            function ($name, $param) use ($tablename) {
                return [
                    'name' => 'constraint',
                    'attributes' => [
                        'xsi:type' => 'foreign',
                        'referenceId' => $name,
                        'table' => $tablename,
                        'column' => $param['column'],
                        'referenceTable' => $param['referenceTable'],
                        'referenceColumn' => $param['referenceColumn'],
                        'onDelete' => $param['onDelete'] ?? 'CASCADE',
                    ],
                ];
            },
            array_keys($fk),
            $fk
        );
    }

    private function getPrimary(DbTable $entity): array
    {
        return [
            'name' => 'constraint',
            'attributes' => [
                'xsi:type' => 'primary',
                'referenceId' => 'PRIMARY',
            ],
            'value' => array_map(function ($column) {
                return [
                    'name' => 'column',
                    'attributes' => [
                        'name' => $column,
                    ],
                ];
            }, $entity->getPrimary()),
        ];
    }

    private function getIndexes(DbTable $entity): array
    {
        $indexes = $entity->getIndexes();

        return array_map(
            function ($name, $param) {
                $param['type'] = $param['type'] ?? 'btree';
                if ('unique' === $param['type']) {
                    return [
                        'name' => 'constraint',
                        'attributes' => [
                            'xsi:type' => 'unique',
                            'referenceId' => $name,
                        ],
                        'value' => array_map(
                            [$this, 'getIndexColumn'],
                            $param['columns']
                        ),
                    ];
                }

                return [
                    'name' => 'index',
                    'attributes' => [
                        'referenceId' => $name,
                        'indexType' => $param['type'],
                    ],
                    'value' => array_map(
                        [$this, 'getIndexColumn'],
                        $param['columns']
                    ),
                ];
            },
            array_keys($indexes),
            $indexes
        );
    }

    private function getIndexColumn($name)
    {
        return [
            'name' => 'column',
            'attributes' => [
                'name' => $name,
            ],
        ];
    }
}
