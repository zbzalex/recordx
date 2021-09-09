<?php

namespace Tests;

use RecordX\AbstractEntity;

class UserEntity extends AbstractEntity
{
    public function getTableName()
    {
        return "users";
    }

    public function getColumns()
    {
        return $this->extend(parent::getColumns(), [
            "name" => [
                "defaultValue" => null
            ],
            "avatar" => [
                "defaultValue" => null
            ],
            "firstName" => [
                "name" => "firstname",
                "defaultValue" => null
            ]
        ]);
    }
}
