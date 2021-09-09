<?php

namespace RecordX;

class DaoRegistry {
    private $dao;
    public function __construct() {
        $this->dao = [];
    }

    public function get($class) {
        return isset($this->dao[$class]) ? $this->dao[$class] : null;
    }

    public function set($class, $object) {
        $this->dao[$class] = $object;

        return $this;
    }
}