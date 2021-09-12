<?php

namespace Tests;

use RecordX\Dao;

class PlayerDao extends Dao {
    public function __getAll() {
        return ["select * from players;", [Player::class]];
    }

    public function __getById() {
        return ["select * from players where id=?;", Player::class];
    }

    public function __getOnlinePlayers($params, $optional) {
        return ["select * from players where last_action>UNIX_TIMESTAMP()-360 order by id desc limit " . $optional[0] . ", " . $optional[1] . ";", [Player::class]];
    }
}