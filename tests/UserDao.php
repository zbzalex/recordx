<?php

namespace Tests;

use RecordX\Dao;

class UserDao extends Dao
{
    public function __getAll()
    {
        return ["select * from users;", [UserEntity::class]];
    }

    public function __deleteAllTest() {
        return ["delete from users where name='test';"];
    }
}
