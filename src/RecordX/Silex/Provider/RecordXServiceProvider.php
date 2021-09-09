<?php

namespace RecordX\Silex\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use RecordX\Connection;
use RecordX\DaoFactory;
use RecordX\DaoProvider;
use RecordX\DaoRegistry;

class RecordXServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container['connection'] = function () use ($container) {
            try {
                return new Connection(new \PDO("mysql:host=" . $container['db.host'] . ";dbname=" . $container['db.database'], $container['db.user'], $container['db.password']));
            } catch (\PDOException $e) {
            }
        };

        $container['dao_registry'] = function () {
            return new DaoRegistry();
        };

        $container['dao_factory'] = function () use ($container) {
            return new DaoFactory($container['dao_registry'], $container['connection']);
        };

        $container['dao_provider'] = function () use ($container) {
            return new DaoProvider($container['dao_factory']);
        };
    }
}
