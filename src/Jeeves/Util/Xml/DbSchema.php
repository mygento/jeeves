<?php

namespace Mygento\Jeeves\Util\Xml;

class DbSchema
{
    public function getPrimary($entity)
    {
        return array_map(
            function ($column, $param) {
                if (!isset($param['pk']) && !isset($param['identity'])) {
                    return [];
                }
                if (isset($param['pk']) && false == $param['pk']) {
                    return [];
                }
                if (isset($param['identity']) && false == $param['identity']) {
                    return [];
                }

                return [
                    'name' => 'column',
                    'attributes' => [
                        'name' => $column,
                    ],
                ];
            },
            array_keys($entity['columns']),
            $entity['columns']
        );
    }

    public function getConstraint($entity, $tablename)
    {
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
            array_keys($entity['fk']),
            $entity['fk']
        );
    }

    public function getIndexes($entity, $tablename)
    {
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
            array_keys($entity['indexes']),
            $entity['indexes']
        );
    }

    public function getIndexFK($entity)
    {
        $indexColumn = array_filter(array_map(
            function ($indx) {
                if (count($indx['columns']) > 1) {
                    return null;
                }

                return $indx['columns'][0];
            },
            array_values($entity['indexes'])
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
            array_values($entity['fk'])
        ));
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
