<?php

namespace RecordX;

abstract class Dao
{
    /** @var \Database\ConnectionInterface $connection  */
    protected $connection;

    private $_lastQueryString;
    private $_lastQueryParams = [];
    private $_error;

    public function setConnection(ConnectionInterface $connection)
    {
        $this->connection = $connection;
    }

    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @return void
     */
    public function insert(AbstractEntity $e)
    {
        $insertColumns = $e->getInsertColumns();
        $values = [];

        for ($i = 0; $i < count($insertColumns); $i++) {
            $values[] = "?";
        }

        $query = "insert into " . $e->getSqlFormatTableName() . " (" . implode(",", static::backslashAll($insertColumns)) . ") values (" . implode(",", $values) . ");";

        $params = [];
        foreach ($e->getColumns() as $column => $options) {
            if (isset($options['primaryKey'])) {
                continue;
            }

            $column = isset($options['name']) ? $options['name'] : $column;
            if (in_array($column, $insertColumns)) {
                $params[] = $e->{$column};
            }
        }

        $this->_lastQueryString = $query;
        $this->_lastQueryParams = $params;

        $st = $this->connection->executeNativeQuery($query, $params);

        $this->_error = $st->errorInfo();

        $id = $this->connection->getPdo()->lastInsertId();

        $primaryKeyColumnName = $e->getPrimaryKeyColumnName();

        $e->{$primaryKeyColumnName} = $id;
    }

    /**
     * @return void
     */
    public function insertArray(array $e)
    {
        for ($i = 0; $i < count($e); $i++) {
            $this->insert($e[$i]);
        }
    }

    /**
     * @return void
     */
    public function delete(AbstractEntity $e)
    {
        $primaryKeyColumnName = $e->getPrimaryKeyColumnName();
        $id = $e->{$primaryKeyColumnName};

        $query = "delete from " . $e->getSqlFormatTableName() . " where " . static::backslash($primaryKeyColumnName) . "=?;";

        $this->_lastQueryString = $query;
        $this->_lastQueryParams = [
            $id,
        ];

        $st = $this->connection->executeNativeQuery($query, [
            $id,
        ]);

        $this->_error = $st->errorInfo();
    }

    public function deleteArray(array $e)
    {
        for ($i = 0; $i < count($e); $i++) {
            $this->delete($e[$i]);
        }
    }

    public function update(AbstractEntity $e)
    {
        $insertColumns = $e->getInsertColumns();
        $primaryKeyColumnName = $e->getPrimaryKeyColumnName();

        $query = "update " . $e->getSqlFormatTableName()
        . " set "
        . implode(",", array_map(function ($column) {
            return static::backslash($column) . "=?";
        }, $insertColumns))
        . " where " . static::backslash($primaryKeyColumnName) . "=?;";
        $params = [];

        foreach ($e->getColumns() as $column => $options) {
            if (isset($options['primaryKey'])) {
                continue;
            }
            
            $column = isset($column['name']) ? $column['name'] : $column;
            if (in_array($column, $insertColumns)) {
                $params[] = $e->{$column};
            }
        }

        $params[] = $e->{$primaryKeyColumnName};

        $this->_lastQueryString = $query;
        $this->_lastQueryParams = $params;

        $st = $this->connection->executeNativeQuery($query, $params);

        $this->_error = $st->errorInfo();
    }

    public function updateArray(array $e)
    {
        for ($i = 0; $i < count($e); $i++) {
            $this->update($e[$i]);
        }
    }

    /**
     * @return AbstractEntity|AbstractEntity[]|null
     */
    public function __call($name, array $args = [])
    {
        $result = call_user_func_array([$this, "__" . $name], $args);
        if (is_array($result) && count($result) > 0) {

            $queryParams = count($args) > 0 ? (array) $args[0] : [];

            $this->_lastQueryString = $result[0];
            $this->_lastQueryParams = $queryParams;

            $st = $this->connection->executeNativeQuery($result[0], $queryParams);

            $this->_error = $st->errorInfo();

            if (isset($result[1])) {
                $isArray = false;
                if (is_array($result[1])) {
                    $entityClass = $result[1][0];
                    $isArray = true;
                } else {
                    $entityClass = $result[1];
                }

                if ($st->rowCount() > 0) {
                    if ($isArray) {
                        $entities = [];
                        while (($row = $st->fetch(\PDO::FETCH_ASSOC))) {
                            $e = $this->_createEntity($entityClass);
                            $this->_setData($e, $row);
                            $entities[] = $e;
                        }

                        return $entities;
                    } else {
                        $e = $this->_createEntity($entityClass);
                        $row = $st->fetch(\PDO::FETCH_ASSOC);
                        $this->_setData($e, $row);
                        return $e;
                    }
                }
            }
        }

        return null;
    }

    private function _createEntity($entityClass)
    {
        try {
            $reflectionClass = new \ReflectionClass($entityClass);

            return $reflectionClass->newInstance();
        } catch (\ReflectionException $e) {
        }

        throw new \RuntimeException("Unable to create entity class `" . $entityClass . "`");
    }

    private function _setData($e, array $data)
    {
        foreach ($e->getColumns() as $column => $options) {

            $primaryKey = isset($options['primaryKey']);
            $type = isset($options['type']) ? $options['type'] : "string";

            $origColumn = isset($options['name']) ? $options['name'] : $column;
            $value = isset($data[$origColumn]) ? $data[$origColumn] : AbstractEntity::getDefaultValueByType($type);

            if ($primaryKey) {
                $value = (int) $value;
            } else {
                switch ($type) {
                    case "int":
                        $value = (int) $value;
                        break;
                    case "float":
                        $value = (float) $value;
                        break;
                    case "double":
                        $value = (double) $value;
                        break;
                }
            }

            $e->{$column} = $value;
        }
    }

    public static function backslash($column)
    {
        return "`" . $column . "`";
    }

    public static function backslashAll($columns)
    {
        return array_map(function ($column) {
            return static::backslash($column);
        }, $columns);
    }

    public function getLastQueryInfo()
    {
        return [
            $this->_lastQueryString,
            $this->_lastQueryParams,
        ];
    }

    public function getError()
    {
        return $this->_error;
    }

    public function isOk()
    {
        return $this->_error[0] == "00000" && $this->_error[1] === null && $this->_error[2] === null;
    }
}
