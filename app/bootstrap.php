<?php
declare(strict_types=1);

error_reporting(E_ALL & ~E_NOTICE & ~E_USER_NOTICE);

use Phalcon\Di\FactoryDefault;
use Phalcon\Http\ResponseInterface;
use Dotenv\Dotenv;
use \Harpya\SDK\Constants;
use \Harpya\SDK\Core\ViewUtils;
use Phalcon\Mvc\Micro;

try {
    $rootPath = realpath('..');
    require_once $rootPath . '/vendor/autoload.php';

    if (file_exists($rootPath . '/.env')) {
        /**
         * Load ENV variables
         */
        Dotenv::createImmutable($rootPath)->load();
    }

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

    $app = \Harpya\IP\Application::getInstance($di);
    $app->loadRoutesFromFolder($rootPath . '/routes');

    ViewUtils::initFilters();
    ViewUtils::addFilters($di);

    // X-Skip-Session
    if (!$app->request->getHeader('X-Skip-Session')) {
        if ($app->cookies->has('sid')) {
            $sid = $this->cookies->get('sid');

            $app->session->setId($sid);
        }

        $app->session->start();
        $lifetime = 600;
        // setcookie(session_name(), session_id(), time() + $lifetime);

        $app->cookies->set(
            session_name(),
            session_id(),
            time() + $lifetime
        );
        $app->cookies->send();
    }

    $localAuthData = $app->session->get('auth_data');

    $app->handle(
        $_SERVER['REQUEST_URI']
    );
} catch (\Exception $e) {
    echo $e->getMessage() . '<br>';
    echo '<pre>' . $e->getTraceAsString() . '</pre>';
}
