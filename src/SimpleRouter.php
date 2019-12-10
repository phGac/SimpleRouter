<?php

namespace SimpleRouter;

use SimpleRouter\SimpleDispatcher;
use SimpleRouter\RouterCollector;

class SimpleRouter {

    public static function dispatcher(\Closure $closure) : SimpleDispatcher
    {
        $dispatcher = new SimpleDispatcher(new RouterCollector());
        $closure($dispatcher->collector());
        return $dispatcher;
    }

}