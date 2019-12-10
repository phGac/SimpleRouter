<?php 

/**
 * Load files
 */
require '../loader.php';

use SimpleRouter\Router;


$__init__ = microtime(true);

$router = new Router();

// set views folder
$router->setView(__DIR__.'/views');

$router->get('/', 'Home::index');
$router->get('/user', 'User::index');
$router->get('/user/{id:\d+}', 'User::show');
$router->route('POST', '/user/{id:\d+}/{firstName:.+}/{lastName:[a-zA-Z]+}', 'User::update');

$router->group('/prefix', function(Router $r){
    $r->get('/{post:[0-9]+}/{test:[A-Za-z]+}', function($post, $test){
        echo "<br>POST:$post, TEST:$test";
    });
    $r->get('/view', function(){
        Router::view('view');
    });
});


$routeInfo = $router->dispatch();
$__finish__ = microtime(true);

$__total__ = ($__finish__ - $__init__);
echo "Time: $__total__ [init:$__init__, finish:$__finish__]<br>";

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
        $params = $routeInfo['params'] ?? [];

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
