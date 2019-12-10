<?php

namespace SimpleRouter;

use \Closure;
use SimpleRouter\RouterCollector;
use SimpleRouter\Dispatcher;

class SimpleDispatcher implements Dispatcher {

    private $collector;

    public function __construct(RouterCollector $collector){
        $this->collector = $collector;
    }

    public function collector() : RouterCollector
    {
        return $this->collector;
    }

    public function dispatch() : array
    {
        $httpMethod = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];
        if (false !== $pos = strpos($uri, '?')) {
            $uri = substr($uri, 0, $pos);
        }
        if( strlen($uri) > 1 && substr($uri, -1) == '/'){
            $uri = substr($uri, 0, -1);
        }
        
        $uri = rawurldecode($uri);
        
        return $this->solveUri($httpMethod, $uri);
    }

    private function solveUri(string $method, string $uri) : array
    {
        // static route
        $routes = $this->collector->staticRoutes();
        if(isset($routes[$method])){
            foreach($routes[$method] as $route => $handler){
                if($route == $uri){
                    return [
                        'status' => self::FOUND,
                        'method' => $method,
                        'params' => null,
                        'handler' => $handler
                    ];
                }
            }
        }

        // dinamic route // --->> algunos problemas
        $uri_split = explode('/', $uri);
        $routes = $this->collector->dinamicRoutes();
        foreach ($routes as $route => $info) {
            if(preg_match($route, $uri, $matches) && ((\sizeof($matches)-2) == (\sizeof($uri_split)-1))){
                if(isset($info[$method])){
                    $vars = array();
                    $i = 2;
                    foreach ($info[$method][1] as $regexName => $regexValue) {
                        if(! \is_numeric($regexName)){
                            $vars[$regexName] = \substr($matches[$i], 1);
                        }
                        $i++;
                    }

                    return [
                        'status' => self::FOUND,
                        'method' => $method,
                        'params' => $vars,
                        'handler' => $info[$method][0]
                    ];
                } else {
                    $methods = array();
                    foreach($info as $allowed => $value){
                        $methods[] = $allowed;
                    }
                    return [
                        'status' => self::METHOD_NOT_ALLOWED,
                        'method' => $method,
                        'allowed-methods' => $methods
                    ];
                }
            }
        }

        // not found
        return [
            'status' => self::NOT_FOUND,
            'method' => $method
        ];
    }

}