<?php

namespace RecordX;

class DaoFactory {
    private $_registry;
    private $_connection;
    public function __construct(DaoRegistry $registry, ConnectionInterface $connection) {
        $this->_registry = $registry;
        $this->_connection = $connection;
    }

    public function create($daoClass) {
        if ($this->_registry->get($daoClass) === null) {
            try {
                $reflectionClass = new \ReflectionClass($daoClass);
                $obj = $reflectionClass->newInstance();
                $obj->setConnection($this->_connection);

                $this->_registry->set($daoClass, $obj);
            } catch(\ReflectionException $e) {
            }
        }

        return $this->_registry->get($daoClass);
    }
}