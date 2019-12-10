<?php

namespace SimpleRouter;

use \Closure;
use SimpleRouter\Router;

class RouterCollector {
    
    private $prefix;

    private $staticRoutes;
    private $dinamicRoutes;

    public function __construct($urls = [], $prefix = "")
    {
        $this->urls = $urls;
        $this->prefix = $prefix;

        $this->staticRoutes = array();
        $this->dinamicRoutes = array();
    }

    public function staticRoutes() : array
    {
        return $this->staticRoutes;
    }

    public function dinamicRoutes() : array
    {
        return $this->dinamicRoutes;
    }

    public function addRoute(string $method, string $url, $handler) : void
    {
        if(! $this->isDinamic($url)){
            $this->staticRoute($method, $url, $handler);
            return;
        } else {
            $this->dinamicRoute($method, $url, $handler);
        }
    }

    public function addGroup(string $prefix, Closure $closure, Router $router) : void 
    {
        $this->prefix = $prefix;
        $closure($router);
        $this->prefix = "";
    }

    private function isDinamic($url){
        return (\strpos($url, '{') != null) ? true : false;
    }

    private function staticRoute($method, $uri, $handler) : void
    {
        $uri = $this->prefix.$uri;
        switch(\strtoupper($method)){
            case "GET":
                $this->staticRoutes['GET'][$uri] = $handler;
                break;
            case "POST":
                $this->staticRoutes['POST'][$uri] = $handler;
                break;
        }
    }

    private function dinamicRoute($method, $uri, $handler) : void
    {
        $uri = $this->prefix.$uri;

        $regex = '{[[:alpha:]]+:(.(?!\/))+}';

        $uri_split = explode('/', $uri);
        $uri_regex = "";
        $uri_params = array();
        foreach ($uri_split as $i => $uri_part) {
            if($uri_part == ''){
                continue;
            } else {
                if( preg_match($regex, $uri_part, $match) ){
                    $match = substr($match[0], 0, -1);
                    $info = explode(':', $match);
                    $uri_regex .= "(\/$info[1])";
                    $uri_params[ $info[0] ] =  '/'.$info[1].'/'; // 0 => name, 1 => value
                } else {
                    $uri_regex .= "(\/$uri_part)";
                    $uri_params[] = $uri_part;
                }
            }
        }

        $uri_regex = "/($uri_regex)/";

        $this->dinamicRoutes[$uri_regex][\strtoupper($method)] = [
            $handler,
            $uri_params
        ];
    }

}