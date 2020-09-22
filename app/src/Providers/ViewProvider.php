<?php
declare(strict_types=1);

namespace Harpya\IP\Providers;

use Phalcon\Di\DiInterface;
use Phalcon\Di\ServiceProviderInterface;
use Phalcon\Mvc\View;

class ViewProvider implements ServiceProviderInterface
{
    public function register(DiInterface $di): void
    {
        $viewsDir = $di->get('rootPath') . DIRECTORY_SEPARATOR . $di->getShared('config')->application->viewsDir;

        $di->setShared('view', function () use ($viewsDir) {
            $view = new View();
            $view->setViewsDir($viewsDir);
            $view->setLayoutsDir($viewsDir . '/layouts/');
            $view->setPartialsDir($viewsDir . '/partials/');

            $view->registerEngines([
                '.volt' => 'volt'
            ]);

            return $view;
        });
    }
}
