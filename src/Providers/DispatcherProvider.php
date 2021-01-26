<?php
declare(strict_types=1);

namespace Invo\Providers;

use Invo\Plugins\NotFoundPlugin;
use Invo\Plugins\SecurityPlugin;
use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Events\Manager;
use Phalcon\Mvc\Dispatcher;

class DispatcherProvider implements ServiceProviderInterface {

    public function register(DiInterface $di): void {

        $di->setShared('dispatcher', function () {

            $manager = new Manager();
            //$manager->attach('dispatch:beforeExecuteRoute', new SecurityPlugin);
            $manager->attach('dispatch:beforeException', new NotFoundPlugin);

            $dispatcher = new class extends Dispatcher {

                //重写 forward 方法支持 module/controller/action 字符串
                public function forward($forward): void {

                    if(is_string($forward)){
                        $array = explode('/',  $forward);
                        array_unshift($array, 0, 0);
                        $array = array_slice($array, -3, 3);
                        $forward = array_filter(['module' => $array[0], 'controller' => $array[1], 'action' => $array[2]]);
                    }
                    parent::forward($forward);
                }
            };

            $dispatcher->setDefaultNamespace('Invo\Controllers');
            $dispatcher->setActionSuffix('');//控制器后缀设置为空
            $dispatcher->setEventsManager($manager);

            return $dispatcher;
        });
    }

}
