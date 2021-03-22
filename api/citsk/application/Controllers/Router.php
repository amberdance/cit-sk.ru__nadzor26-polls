<?php

namespace Citsk\Controllers;

use Citsk\Exceptions\RouterException;
use Citsk\Interfaces\Controllerable;

final class Router
{

    /**
     * @var string
     */
    private $requestedAction;

    /**
     * @var string
     */
    private $controllerNamespace = "Citsk\Controllers\\";

    /**
     * @var string
     */
    private $controllerName;

    /**
     * @return Router
     */
    public function initializeParameters(): Router
    {

        $parts  = parse_url($_SERVER['REQUEST_URI']);
        $params = explode('/', $parts['path']);

        $this->controllerName = $params[2];
        preg_match("/^([^?]+)(\?.*?)?(#.*)?$/", $params[3], $matches);
        $this->requestedAction = $matches[1];

        return $this;
    }

    /**
     * @return Router
     */
    public function setHTTPHeaders(): Router
    {

        if ($_SERVER['REQUEST_URI'] == '/index.php' || $_SERVER['REQUEST_URI'] == '/') {
            die(http_response_code(403));
        }

        if (isset($_SERVER['HTTP_ORIGIN'])) {
            header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
            header('Access-Control-Allow-Credentials: true');
            header('Access-Control-Max-Age: 86400');
        }

        if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD'])) {
                header("Access-Control-Allow-Methods: GET, POST");
            }

            if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS'])) {
                header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");
            }

            die();
        }

        return $this;
    }

    /**
     * @return object
     */
    public function initializeRouting(): void
    {
        try {
            $this->getControllerName();
            $this->initializeController();
        } catch (RouterException $e) {

            $params = [
                "error"  => $e->getMessage(),
                "status" => $e->getCode(),
            ];

            exit(json_encode($params));
        }
    }

    /**
     * @return void
     */
    private function getControllerName(): void
    {

        $this->controllerName = $this->controllerNamespace . ucfirst($this->controllerName) . "Controller";
    }

    /**
     * @return bool
     */
    private function isExistsController(?string $controllerName = null): bool
    {
        return class_exists($controllerName ?? $this->controllerName) ? true : false;
    }

    /**
     * @return void
     */
    private function initializeController(): void
    {
        $controller = new $this->controllerName;

        if ($controller instanceof Controllerable) {
            global $ROUTE;

            $ROUTE['action']        = $this->requestedAction;
            $ROUTE['original_path'] = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

            if (empty($_POST)) {
                $_POST = json_decode(file_get_contents('php://input'), true);
            }

            $controller->initializeController();
            $controller->callRequestedMethod();
        } else {
            throw new RouterException("{$this->controllerName} is not the controller");
        }
    }
}
