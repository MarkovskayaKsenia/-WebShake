<?php
//header('content-type: application/json');
try {

    spl_autoload_register(function (string $className) {
        require_once __DIR__ . '/../src/' . $className . '.php';
    });

    $route = $_GET['route'] ?? '';
    $routes = require __DIR__ . '/../src/routes.php';

    $isRouteFound = false;

    foreach ($routes as $pattern => $controllerAndAction) {
        preg_match($pattern, $route, $matches);
        if (!empty($matches)) {
            $isRouteFound = true;
            break;
        }
    }

    if (!$isRouteFound) {
        throw new \NewProject\Exceptions\NotFoundException();
    }

    unset($matches[0]);

    $controllerName = $controllerAndAction[0];
    $actionName = $controllerAndAction[1];

    $controller = new $controllerName();
    $controller->$actionName(...$matches);
} catch (\NewProject\Exceptions\DbException $e) {
    $view = new \NewProject\View\View(__DIR__ . '/../templates/errors/');
    $view->renderHtml('500.php', ['error' =>$e->getMessage()], 500);
} catch(\NewProject\Exceptions\NotFoundException $e) {
    $view = new \NewProject\View\View(__DIR__ . '/../templates/errors/');
    $view->renderHtml('404.php', ['error' => $e->getMessage()], 404);
} catch (\NewProject\Exceptions\UnauthorizedException $e) {
    $view = new \NewProject\View\View(__DIr__ . '/../templates/errors');
    $view->renderHtml('401.php', ['error' =>$e->getMessage()], 401);
} catch (\NewProject\Exceptions\ForbiddenException $e) {
    $view = new \NewProject\View\View(__DIr__ . '/../templates/errors');
    $view->renderHtml('403.php', ['error' =>$e->getMessage()], 403);
}