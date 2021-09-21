<?php

namespace Tests;

use RecordX\Connection;
use RecordX\DaoFactory;
use RecordX\DaoProvider;
use RecordX\DaoRegistry;
use PHPUnit\Framework\TestCase;

class DaoTest extends TestCase
{
    public function testDao()
    {
        $pdo = new \PDO("mysql:host=localhost;dbname=darkworld", "root", "123");
        $connection = new Connection($pdo);
        $registry = new DaoRegistry();
        $factory = new DaoFactory($registry, $connection);
        $provider = new DaoProvider($factory);

        // $userDao = $provider->get(UserDao::class);

        //$user = new UserEntity();
        //$user->name = "test";

        //var_dump($user);

        //$userDao->insert($user);

        // $users = $userDao->getAll();
        // var_dump($users[1]->firstName . " " . $users[1]->lastName);

        //$userDao->deleteAllTest();

        //var_dump($user->id);

        // $users = $userDao->getAll();
        //var_dump($users);

        // $user = $users[0];

        // var_dump($user->getFirstName());

        // $user->setFirstName("Alexandr");

        // var_dump($user->getFirstName());

        // $userDao->updateArray([$user]);
        // $userDao->update($user);

        $playerDao = $provider->get(PlayerDao::class);

        $player = new Player();
        $player->name = "test123";
        $player->id = 32;

        //$playerDao->insert($player);

        $playerDao->update($player);

        var_dump($playerDao->getLastQueryInfo());
        

        // $players = $playerDao->getAll();

        // var_dump($players);
        
        // $player = $playerDao->getById(1);
        // var_dump($player);

        //$players = $playerDao->getOnlinePlayers(null, [0, 10]);
        //var_dump($players);

        // var_dump($userDao->getLastQueryInfo());
        // var_dump($userDao->getError());
        // var_dump($userDao->isOk());

        // var_dump($userDao->getLastQueryInfo());
        // var_dump($userDao->getError());
        // var_dump($userDao->isOk());

        // echo $user->getAvatar();
        // $user->setAvatar("240.jpg");
        // echo $user->getAvatar();
    }
}
