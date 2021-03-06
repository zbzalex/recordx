<?php

namespace Tests;

use RecordX\AbstractEntity;

class Player extends AbstractEntity
{
    public function getTableName()
    {
        return "players";
    }

    public function getColumns()
    {
        return $this->extend(parent::getColumns(), [
            "accountId" => [
                "name" => "account_id",
                "type" => "int",
            ],
            "name" => [
                "type" => "string",
            ],
        ]);
    }
}
