<?php
declare(strict_types=1);

namespace Invo\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;

class DatabaseProvider implements ServiceProviderInterface {

    public function register(DiInterface $di): void {

        $config = $di->getShared('config')->get('database')->toArray();
        $profiler = $di->getProfiler();

        $di->setShared('db', function () use ($config, $profiler) {

            $manager = new \Phalcon\Events\Manager();
            $manager->attach('db', function($event, $connection) use ($profiler) {

                if ($event->getType() == 'beforeQuery') {
                    $profiler->startProfile($connection->getSQLStatement());
                }
                if ($event->getType() == 'afterQuery') {
                    $profiler->stopProfile();
                }
            });

            $Adapter = 'Phalcon\Db\Adapter\Pdo\\' . $config['adapter'];
            unset($config['adapter']);
            $connect = new $Adapter($config);
            $connect->setEventsManager($manager);

            return $connect;
        });
    }

}
