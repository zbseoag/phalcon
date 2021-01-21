<?php
declare(strict_types=1);

/**
 * This file is part of the Invo.
 *
 * (c) Phalcon Team <team@phalcon.io>
 *
 * For the full copyright and license information, please view
 * the LICENSE file that was distributed with this source code.
 */

namespace Invo\Providers;

use Invo\Plugins\NotFoundPlugin;
use Invo\Plugins\SecurityPlugin;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;

/**
 * We register the events manager
 */
class DispatcherProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $di->setShared('dispatcher', function () {

            $manager = new Manager();
            $manager->attach('dispatch:beforeExecuteRoute', new SecurityPlugin);
            $manager->attach('dispatch:beforeException', new NotFoundPlugin);

            $dispatcher = new Dispatcher();
            $dispatcher->setDefaultNamespace('Invo\Controllers');
            //$dispatcher->setActionSuffix('');
            $dispatcher->setEventsManager($manager);

            return $dispatcher;
        });
    }
}
