<?php

namespace RecordX;

class Connection implements ConnectionInterface
{
    /**
     * @var \PDO $_pdo
     */
    private $_pdo;
    
    public function __construct(\PDO $pdo)
    {
        $this->_pdo = $pdo;
    }
    
    public function getPdo()
    {
        return $this->_pdo;
    }

    public function executeNativeQuery($query, array $params = [])
    {
        $st = $this->_pdo->prepare($query);
        $st->execute($params);

        return $st;
    }
}
