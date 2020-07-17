<?php

namespace NewProject\Models;

use NewProject\Services\Db;

abstract class ActiveRecordEntity {

    protected $id;

    public function __set($name, $value)
    {
        $camelCaseName = $this->underscoreToCamelCase($name);
        $this->$camelCaseName = $value;
    }

    public function getId(): int
    {
        return (int) $this->id;
    }

    public static function getById( int $id): ?self
    {
        $db = Db::getInstanse();
        $entities  = $db->query(
            'SELECT * FROM ' . static::getTableName() . ' WHERE id = :id;',
            ['id' => $id],
            static::class
        );
        return $entities ? $entities[0] : null;
    }

    public static function findAll(): array
    {
        $db = Db::getInstanse();
        return $db->query('SELECT * FROM `' . static::getTableName() . '`;', [], static::class);
    }

    public function save(): void
    {
        $mappedProperties = $this->mapPopertiesToDbFormat();
        if ($this->id !== null) {
            $this->update($mappedProperties);
        } else {
            $this->insert($mappedProperties);
        }
    }

    public function delete(): void
    {
        $db = Db::getInstanse();
        $sql = 'DELETE FROM ' . static::getTableName() . ' WHERE id = :id;';
        $db->query($sql, ['id' => $this->id]);
        $this->id = null;
    }

    public static function findByOneColumn(string $columnName, $value)//: ?self
    {
        $db = Db::getInstanse();
        $sql = 'SELECT * FROM ' . static::getTableName() . " WHERE $columnName = :value LIMIT 1;";
        $result = $db->query($sql, ['value' => $value], static::class);

        return ($result) ? $result[0] : null;

    }

    private function update(array $mappedProperties): void
    {
        $columnsToParams = [];
        $paramsToValues = [];
        $index = 1;
        foreach ($mappedProperties as $column => $value) {
            $param = ':param' . $index;
            $columnsToParams[] = $column . ' = ' . $param;
            $paramsToValues[$param] =  $value;
            $index++;
        }

        $sql = 'UPDATE ' . static::getTableName() . ' SET ' . implode(', ', $columnsToParams)
            . " WHERE id = " . $this->id;
        $db = Db::getInstanse();
        $db->query($sql, $paramsToValues, static::class);
    }

    private function insert(array $mappedProperties): void
    {
        $filteredProperties = array_filter($mappedProperties);

        $columns = [];
        $paramsNames = [];
        $paramsToValues = [];
        foreach ($filteredProperties as $columnName => $value) {
            $columns [] = '`' . $columnName . '`';
            $paramName = ':' . $columnName;
            $paramsNames[] = $paramName;
            $paramsToValues[$paramName] = $value;
        }
        $columnsViaSemicolon = implode(', ', $columns);
        $paramsNamesViaSemicolon = implode(', ', $paramsNames);

        $sql = 'INSERT INTO ' . static::getTableName() . " ($columnsViaSemicolon) VALUES ($paramsNamesViaSemicolon); ";
        $db = Db::getInstanse();
        $db->query($sql, $paramsToValues, static::class);
        $this->id = $db->getLastInsertId();
        $this->refresh();
    }

    private function refresh(): void
    {
        $dbObject = static::getById($this->id);
        $properties = get_object_vars($dbObject);
        foreach($properties as $key => $value) {
            $this->$key = $value;
        }
    }

    private function underscoreToCamelCase(string $source): string
    {
        return lcfirst(str_replace('_', '', ucwords($source, '_')));
    }

    private function camelCaseToUnderscore(string $source): string
    {
        return strtolower(preg_replace('~(?<!^)[A-Z]~', '_$0', $source));
    }

    private function mapPopertiesToDbFormat(): array
    {
        $reflector = new \ReflectionObject($this);
        $properies = $reflector->getProperties();

        $mappedProperties = [];
        foreach ($properies as $property) {
            $propertyName = $property->getName();
            $propertyNameAsUnderscore = $this->camelCaseToUnderscore($propertyName);
            $mappedProperties[$propertyNameAsUnderscore] = $this->$propertyName;
        }

        return $mappedProperties;
    }

    abstract protected static function getTableName(): string;
}
