<?php

namespace RecordX;

interface ConnectionInterface
{
    public function getPdo();
    public function executeNativeQuery($query, array $params = []);
}
