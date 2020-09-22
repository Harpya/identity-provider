<?php
declare(strict_types=1);

error_reporting(E_ALL & ~E_NOTICE);

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\ResponseInterface;
use Dotenv\Dotenv;

try {
    $rootPath = realpath('..');
    require_once $rootPath . '/vendor/autoload.php';

    /**
     * Load ENV variables
     */
    Dotenv::createImmutable($rootPath)->load();

    $di = new FactoryDefault();

    $di->offsetSet('rootPath', function () use ($rootPath) {
        return $rootPath;
    });

    /**
     * Register Service Providers
     */
    $providers = $rootPath . '/config/providers.php';
    if (!file_exists($providers) || !is_readable($providers)) {
        throw new Exception('File providers.php does not exist or is not readable.');
    }

    /** @var array $providers */
    $providers = include_once $providers;
    foreach ($providers as $provider) {
        $di->register(new $provider());
    }

    $router = $di['router'];

    $handle = $router->handle(
        $_SERVER['REQUEST_URI']
    );

    $view = $di['view'];

    // View
    $view->start();

    $dispatcher = $di['dispatcher'];

    $controllerName = $router->getControllerName() ?? 'index';

    $dispatcher->setControllerName(
        $controllerName
    );

    $actionName = $router->getActionName() ?? 'index';

    $dispatcher->setActionName(
        $actionName
    );

    $params = $router->getParams();

    $dispatcher->setParams(
        $params
    );

    $request = new \Phalcon\Http\Request();

    // $customRouter = new \AltoRouter();
    $customRouter = new \Harpya\IP\Lib\Router($di);

    $customRouter->loadFromFolder($rootPath . '/routes');

    $match = $customRouter->match();

    if (is_array($match) && is_callable($match['target'])) {
        $statusCode = 200;
        try {
            $resp = call_user_func_array($match['target'], $match['params']);
        } catch (\Exception $ex) {
            $resp = [
                'msg' => $ex->getMessage()
            ];
            $statusCode = $ex->getCode();
        }

        if (is_array($resp)) {
            $resp = \json_encode($resp);
            $response = new Phalcon\Http\Response($resp, $statusCode);
            $response->setContentType('application/json');
        } else {
            $response = new Phalcon\Http\Response($resp, $statusCode);
        }
    } else {
        // no route was matched

        $controller = $dispatcher->dispatch();

        // View
        $view->render(
        $dispatcher->getControllerName(),
        $dispatcher->getActionName(),
        $dispatcher->getParams()
    );

        // View
        $view->finish();

        $response = $controller->response;
        //$dispatcher->getReturnedValue();
    }

    // If controller did  return nothing, get the view.
    if (!$response->getContent()) {
        $response->setContent(
            $view->getContent()
        );
    }

    if ($response instanceof ResponseInterface) {
        $response->send();
    }
} catch (Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}