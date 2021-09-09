<?php

namespace RecordX;

class DaoProvider {
    private $_factory;
    public function __construct(DaoFactory $factory) {
        $this->_factory = $factory;
    }

    public function get($daoClass) {
        return $this->_factory->create($daoClass);
    }
}