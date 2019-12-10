<?php 

/**
 * Load files
 */
require '../loader.php';

$__init__ = microtime(true);

$dispatcher = SimpleRouter\SimpleRouter::dispatcher( function(SimpleRouter\RouterCollector $router) {
    $router->addRoute('GET', '/', 'Home::index');
    $router->addRoute('GET', '/user', 'User::index');
    $router->addRoute('GET', '/user/{id:\d+}', 'User::show');
    $router->addRoute('POST', '/user/{id:\d+}/{firstName:.+}/{lastName:[a-zA-Z]+}', 'User::update');

    $router->addGroup('/prefix', function(SimpleRouter\RouterCollector $r){
        $r->addRoute('GET', '/{post:[0-9]}/{test:[A-Za-z]}', function($post, $test){
            echo "<br>POST:$post, TEST:$test";
        });
    });
});


$routeInfo = $dispatcher->dispatch();
$__finish__ = microtime(true);

$__total__ = ($__finish__ - $__init__);
echo "Time: $__total__ [init:$__init__, finish:$__finish__]";

switch($routeInfo['status']){
    case SimpleRouter\Dispatcher::NOT_FOUND:
        echo "ROUTE NOT FOUND";
        break;
    case SimpleRouter\Dispatcher::METHOD_NOT_ALLOWED:
        echo "METHOD NOT ALLOWED. ALLOWED METHODS: ".implode(', ', $routeInfo['allowed-methods']);
        break;
    case SimpleRouter\Dispatcher::FOUND:
        echo "ROUTE FOUND :D!";

        $handler = $routeInfo['handler'];
        $params = $routeInfo['params'];

        if($handler instanceof closure) {
            $handler( ...$params );
        } else {
            $handler = explode('::', $handler);

            $class = $handler[0];
            $method = $handler[1];
    
            // call method of the class
            //$class->$method( ...$params );
        }
        break;
}
