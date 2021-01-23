<?php
declare(strict_types=1);

namespace Invo\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;


class ProfilerProvider implements ServiceProviderInterface {

    public function register(DiInterface $di): void {

        $di->setShared('profiler', function () {

            return new \Phalcon\Db\Profiler();
        });
    }

}
