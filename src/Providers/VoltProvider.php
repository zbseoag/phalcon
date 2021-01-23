<?php
declare(strict_types=1);

namespace Invo\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\View\Engine\Volt;

class VoltProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $view = $di->getShared('view');

        $di->setShared('volt', function () use ($view, $di) {
            $volt = new Volt($view, $di);
            $volt->setOptions([
                'path' => $di->offsetGet('rootPath') . '/var/cache/volt/',
            ]);

            $compiler = $volt->getCompiler();
            $compiler->addFunction('is_a', 'is_a');

            return $volt;
        });
    }
}
