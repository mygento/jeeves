<?php

namespace Mygento\Jeeves\Generators\Crud;

class Common
{
    const DEFAULT_FIELDS = ['id' => ['type' => 'int']];

    /**
    * Converts an input string from snake_case to upper CamelCase.
    *
    * @param string $input
    * @return string
    */
    public function snakeCaseToUpperCamelCase($input)
    {
        return str_replace(' ', '', ucwords(str_replace('_', ' ', $input)));
    }

    /**
    * Converts an input string from snake_case to upper CamelCase.
    *
    * @param string $input
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
     * @return string
     */
    public function snakeCaseToCamelCase($input)
    {
        return lcfirst(self::snakeCaseToUpperCamelCase($input));
    }

    /**
     * Convert a CamelCase string read from method into field key in snake_case
     *
     * For example [DefaultShipping => default_shipping, Postcode => postcode]
     *
     * @param string $name
     * @return string
     */
    public function camelCaseToSnakeCase($name)
    {
        return strtolower(preg_replace('/(.)([A-Z])/', '$1_$2', $name));
    }

    /**
    * @param string $name
    * @param mixed $type
    * @return string
    */
    public function convertType($type)
    {
        switch ($type) {
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
            case 'decimal':
            case 'real':
            case 'double':
                return 'float';
            default:
                return $type;
        }
    }
}
