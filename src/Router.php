<?php

namespace SimpleRouter;

use \Closure;
use SimpleRouter\RouterCollector;
use SimpleRouter\Dispatcher;
use SimpleRouter\SimpleDispatcher;

class Router {

    private $collector;
    private $dispatcher;

    private static $view_folder;

    public function __construct(string $view_folder = ''){
        $this->collector = new RouterCollector();
        $this->dispatcher = new SimpleDispatcher();
        Router::$view_folder = $view_folder;
    }

    public function route(string $method, string $url, $handler){
        $this->collector->addRoute($method, $url, $handler);
    }

    public function group(string $prefix, Closure $closure){
        $this->collector->addGroup($prefix, $closure, $this);
    }

    public function get(string $url, $handler){
        $this->collector->addRoute('GET', $url, $handler);
    }

    public function post(string $url, $handler){
        $this->collector->addRoute('POST', $url, $handler);
    }

    public function put(string $url, $handler){
        $this->collector->addRoute('PUT', $url, $handler);
    }

    public function dispatch() : array
    {
        return $this->dispatcher->dispatch($this->collector);
    }

    public static function view(string $viewName) : void
    {
        if(self::$view_folder == '')
            throw new \Exception("View folder has not set. Use 'setView' method to fix");
        require self::$view_folder.'/'.$viewName.'.php';
    }

    public function setView(string $path) : void
    {
        self::$view_folder = $path;
    }

}
