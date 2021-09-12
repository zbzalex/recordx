<?php

namespace RecordX;

abstract class AbstractEntity
{
    protected $values = [];

    public function getTableName()
    {
        return "";
    }

    public function getColumns()
    {
        return [
            "id" => [
                "type" => "int",
                "primaryKey" => true,
            ],
        ];
    }

    public function __get($key)
    {
        $columns = $this->getColumns();
        $type = isset($columns[$key]) && isset($columns[$key]['type']) ? $columns[$key]['type'] : "string";
        $defaultValue = isset($columns[$key]) && isset($columns[$key]['defaultValue']) ? $columns[$key]['defaultValue'] : static::getDefaultValueByType($type);
        
        return isset($columns[$key]) && isset($this->values[$key]) ? $this->values[$key] : $defaultValue;
    }

    public static function getDefaultValueByType($type)
    {
        switch ($type) {
            case "int":
            case "float":
            case "double":
                return 0;
            default:
                return null;
        }
    }

    public function __set($key, $value)
    {
        $columns = $this->getColumns();
        if (isset($columns[$key])) {
            $this->values[$key] = $value;
        }
    }

    public function getInsertColumns()
    {
        $columns = [];
        foreach ($this->getColumns() as $column => $options) {
            if (isset($options['primaryKey'])) {
                continue;
            }

            $columns[] = isset($options['name']) ? $options['name'] : $column;
        }

        return $columns;
    }

    public function getPrimaryKeyColumnName()
    {
        foreach ($this->getColumns() as $column => $options) {
            if (isset($options['primaryKey'])) {
                return $column;
            }
        }

        return null;
    }

    public function __call($name, array $args = [])
    {
        if (substr($name, 0, 3) == "get") {
            $origColumn = substr($name, 3);
            $column = strtolower(substr($origColumn, 0, 1)) . substr($origColumn, 1);
            $columns = $this->getColumns();
            if (isset($columns[$column]) && isset($this->values[$column])) {
                return $this->values[$column];
            }
        } else if (substr($name, 0, 3) == "set") {
            $origColumn = substr($name, 3);
            $column = strtolower(substr($origColumn, 0, 1)) . substr($origColumn, 1);
            $columns = $this->getColumns();
            if (isset($columns[$column])) {
                if (count($args) != 1) {
                    throw new \InvalidArgumentException();
                }

                $this->values[$column] = $args[0];
            }
        }
    }

    protected function extend(array $parent, array $child)
    {
        foreach ($child as $column => $options) {
            $parent[$column] = $options;
        }

        return $parent;
    }

    public function getSqlFormatTableName()
    {
        $tableName = $this->getTableName();

        return is_array($tableName) && count($tableName) == 2
        ? "`" . $tableName[0] . "`.`" . $tableName[1] . "`"
        : "`" . $tableName . "`";
    }
}
