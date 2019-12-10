<?php

namespace SimpleRouter;

interface Dispatcher {
    public const NOT_FOUND = 0;
    public const FOUND = 1;
    public const METHOD_NOT_ALLOWED = 2;

    public function dispatch() : array;
}