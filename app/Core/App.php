<?php

namespace App\Core;

class App {
    protected $controller = 'HomeController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        if (isset($url[0])) {
            // Huruf pertama besar untuk nama Controller
            $controllerName = ucfirst($url[0]) . 'Controller';
            $controllerClass = 'App\\Controllers\\' . $controllerName;

            if (class_exists($controllerClass)) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        $controllerClass = 'App\\Controllers\\' . $this->controller;
        
        // Cek apakah controller yang dituju tidak ada
        if (!class_exists($controllerClass)) {
             die("Controller {$this->controller} tidak ditemukan!");
        }

        $this->controller = new $controllerClass;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        if (!empty($url)) {
            $this->params = array_values($url);
        }

        call_user_func_array([$this->controller, $this->method], $this->params);
    }

    public function parseUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
