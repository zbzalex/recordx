<?php

namespace RecordX;

interface ConnectionInterface
{
    /**
     * @return PDO
     */
    public function getPdo();
    
    public function executeNativeQuery($query, array $params = []);
}
