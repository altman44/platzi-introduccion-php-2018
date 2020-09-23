<?php

use Aura\Router\RouterContainer;

$request = \Laminas\Diactoros\ServerRequestFactory::fromGlobals(
    $_SERVER,
    $_GET,
    $_POST,
    $_COOKIE,
    $_FILES
);

$routerContainer = new RouterContainer();
$route = router($request, $routerContainer);

if (!$route) {
    echo 'No route.';
} else {
    $handlerData = $route->handler;
    $controllerName = $handlerData['controller'];
    $actionName = $handlerData['action'];
    $authRequired = $handlerData['auth_required'] ?? false;
    $permissionToNavigate = true;

    $sessionUserId = $_SESSION['userId'] ?? null;
    if ($authRequired && !$sessionUserId) {
        $secondaryControllerName = $handlerData['secondaryController'] ?? null;
        $secondaryActionName = $handlerData['secondaryAction'] ?? null;
        if ($secondaryControllerName && $secondaryActionName) {
            $controllerName = $secondaryControllerName;
            $actionName = $secondaryActionName;
        } else {
            $permissionToNavigate = false;
        }
    }

    if (!$permissionToNavigate) {
        echo 'Protected route!';
    } else {
        $controller = new $controllerName;
        $response = $controller->$actionName($request);
        foreach ($response->getHeaders() as $name => $values) {
            foreach ($values as $value) {
                header(sprintf('%s: %s', $name, $value), false);
            }
        }
        http_response_code($response->getStatusCode());
        echo $response->getBody();
    }
}

function router($request, $routerContainer)
{
    $map = $routerContainer->getMap();
    $map->get('home', BASE_ROUTE . '/', [
        'controller' => 'App\Controllers\HomeController',
        'action' => 'homeAction'
    ]);

    $map->get('go_to_login', BASE_ROUTE . '/login', [
        'controller' => 'App\Controllers\AuthController',
        'action' => 'loginAction'
    ]);

    $map->post('login_data', BASE_ROUTE . '/login', [
        'controller' => 'App\Controllers\AuthController',
        'action' => 'loginAction'
    ]);

    $map->get('go_to_register', BASE_ROUTE . '/register', [
        'controller' => 'App\Controllers\AuthController',
        'action' => 'registerAction'
    ]);

    $map->post('register_data', BASE_ROUTE . '/register', [
        'controller' => 'App\Controllers\AuthController',
        'action' => 'registerAction'
    ]);

    $map->get('logout', BASE_ROUTE . '/logout', [
        'controller' => 'App\Controllers\AuthController',
        'action' => 'logoutAction',
        'auth_required' => true,
        'secondaryController' => 'App\Controllers\HomeController',
        'secondaryAction' => 'homeAction'
    ]);

    $map->get('addAptitudes', BASE_ROUTE . '/aptitudes/add', [
        'controller' => 'App\Controllers\AptitudesController',
        'action' => 'getAddAptitudeAction',
        'auth_required' => true,
        'secondaryController' => 'App\Controllers\AuthController',
        'secondaryAction' => 'loginAction'
    ]);

    $map->post('saveAptitudes', BASE_ROUTE . '/aptitudes/add', [
        'controller' => 'App\Controllers\AptitudesController',
        'action' => 'getAddAptitudeAction',
        'auth_required' => true,
        'secondaryController' => 'App\Controllers\AuthController',
        'secondaryAction' => 'loginAction'
    ]);

    $map->get('dashboard', BASE_ROUTE . '/dashboard', [
        'controller' => 'App\Controllers\DashboardController',
        'action' => 'indexAction',
        'auth_required' => true,
        'secondaryController' => 'App\Controllers\AuthController',
        'secondaryAction' => 'loginAction'
    ]);

    $matcher = $routerContainer->getMatcher();
    $route = $matcher->match($request);
    return $route;
}
