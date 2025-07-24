<?php

namespace App\Core;

class App
{

    protected $controller = 'home';
    protected $method = 'index';
    protected $params;

    public function __construct()
    {
        $url = $this->parseURL();

        $controllerName = ucfirst(strtolower($url[0] ?? 'home')) . 'Controller';
        $controllerClass = "App\\Controllers\\$controllerName";

        if (class_exists($controllerClass)) {
            $this->controller = new $controllerClass();
            unset($url[0]);
        } else {
            http_response_code(404);
            die("Controller '{$controllerClass}' not found.");
        }

        if (isset($url[1])) {
            $url[1] = strtolower($url[1]);
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = array_values($url);

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseURL()
    {

        $url = isset($_GET['url']) ? $_GET['url'] : 'home';
        return explode('/', filter_var(trim($url, '/')), FILTER_SANITIZE_URL);
    }
}
