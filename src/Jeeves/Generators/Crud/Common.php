<?php

namespace Mygento\Jeeves\Generators\Crud;

use Sabre\Xml\Service;

class Common
{
    protected const DEFAULT_FIELDS = ['id' => ['type' => 'int']];
    protected const TAB = '    ';
    protected const DEFAULT_KEY = 'id';
    protected const A = 'attributes';
    protected const N = 'name';
    protected const V = 'value';

    /**
     * Converts an input string from snake_case to upper CamelCase.
     *
     * @param string $input
     *
     * @return string
     */
    public function snakeCaseToUpperCamelCase($input)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
    }

    /**
     * Converts an input string from snake_case to upper Camel Case.
     *
     * @param string $input
     *
     * @return string
     */
    public function snakeCaseToUpperCamelCaseWithSpace($input)
    {
        return ucwords(str_replace('_', ' ', $input));
    }

    /**
     * Converts an input string from snake_case to camelCase.
     *
     * @param string $input
     *
     * @return string
     */
    public function snakeCaseToCamelCase($input)
    {
        return lcfirst(self::snakeCaseToUpperCamelCase($input));
    }

    /**
     * Convert a CamelCase string read from method into field key in snake_case.
     *
     * For example [DefaultShipping => default_shipping, Postcode => postcode]
     *
     * @param string $name
     *
     * @return string
     */
    public function camelCaseToSnakeCase($name)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $name));
    }

    public function camelCaseToSnakeCaseNoUnderscore($name)
    {
        return str_replace('_', '', $this->camelCaseToSnakeCase($name));
    }

    public function getLowerCaseModuleEntity($module, $entity)
    {
        return $this->camelCaseToSnakeCase($module) . '_' . str_replace('_', '', $this->camelCaseToSnakeCase($entity));
    }

    /**
     * Split At UpperCase.
     */
    public function splitAtUpperCase(string $s): string
    {
        return implode(' ', preg_split('/(?=[A-Z])/', $s, -1, PREG_SPLIT_NO_EMPTY));
    }

    public function getEntityPrintName(string $entity): string
    {
        return $this->splitAtUpperCase($this->snakeCaseToUpperCamelCase($entity));
    }

    public function getEntityName(string $entity): string
    {
        return $this->snakeCaseToUpperCamelCase($entity);
    }

    /**
     * @param string $type
     *
     * @return string
     */
    public function convertType($type)
    {
        switch ($type) {
            case 'store':
                return 'array';
            case 'smallint':
            case 'bigint':
            case 'tinyint':
                return 'int';
            case 'boolean':
                return 'bool';
            case 'blob':
            case 'datetime':
            case 'date':
            case 'varchar':
            case 'timestamp':
            case 'varbinary':
            case 'text':
            case 'mediumtext':
            case 'longtext':
                return 'string';
            case 'price':
            case 'decimal':
            case 'real':
            case 'double':
                return 'float';
            default:
                return $type;
        }
    }

    protected function isNullable(array $value): bool
    {
        if ($value['type'] === 'boolean' && !isset($value['nullable'])) {
            return true;
        }

        return isset($value['nullable']) && $value['nullable'] === false;
    }

    protected function getService(): Service
    {
        $service = new Service();
        $service->namespaceMap = ['http://www.w3.org/2001/XMLSchema-instance' => 'xsi'];

        return $service;
    }
}
