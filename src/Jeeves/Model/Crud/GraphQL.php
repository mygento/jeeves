<?php

namespace Mygento\Jeeves\Model\Crud;

use Mygento\Jeeves\IO\IOInterface;
use Mygento\Jeeves\Model\Generator;

class GraphQL extends Generator
{
    public function __construct(IOInterface $io)
    {
        $this->setIO($io);
    }

    public function generateSchema(Entity $entity)
    {
        $filePath = $entity->getPath() . '/etc/';
        $columns = $this->getFields($entity);
        $this->writeFile(
            $filePath . 'schema.graphqls',
            implode(PHP_EOL, [
                'type ' . $entity->getEntityApiName() . ' @doc(description: "' . ($entity->getComment() ?: $entity->getPrintName()) . '") {',
                implode(
                    PHP_EOL,
                    $columns,
                ),
                '}',
            ]),
        );
    }

    private function getFields(Entity $entity)
    {
        $result = array_map(
            function (string $column, array $param) {
                if (isset($param['pk']) && $param['pk'] === true) {
                    return null;
                }

                $nullable = $param['nullable'] ?? true;

                switch ($param['type']) {
                    case 'boolean':
                        $nullable = false;
                        $type = 'Boolean';
                        break;
                    case 'blob':
                    case 'varbinary':
                    case 'json':
                        return null;
                    case 'int':
                    case 'smallint':
                    case 'bigint':
                    case 'tinyint':
                        $type = 'Int';
                        break;
                    case 'price':
                        $type = 'Money';
                        break;
                    case 'real':
                    case 'decimal':
                    case 'float':
                    case 'double':
                        $type = 'Float';
                        break;
                    case 'text':
                    case 'mediumtext':
                    case 'longtext':
                    case 'date':
                    case 'datetime':
                    case 'timestamp':
                    case 'varchar':
                        $type = 'String';
                        break;
                    default:
                        throw new \Exception('Error column type');
                }

                return self::TAB . $column . ': ' . $type . (!$nullable ? '!' : '') . ' @doc(description: "' . ($param['comment'] ?? ucfirst($column)) . '")';
            },
            array_keys($entity->getColumns()),
            $entity->getColumns(),
        );

        return array_filter($result);
    }
}
