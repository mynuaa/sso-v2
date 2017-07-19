<?php

/**
 * 后端路由
 * Author: Rex
 */

function addRouter($method, $route, $controller) {
    $segments = explode('@', $controller);
    $reflectionMethod = new ReflectionMethod($segments[0], $segments[1]);
    $depends = [];
    foreach ($reflectionMethod->getParameters() as $value) {
        $type = $value->getType();
        if ($type) {
            $class = $type->__toString();
            $depends []= $class;
        }
    }
    $func = function () use ($segments, $depends) {
        foreach ($depends as &$value) {
            $value = new $value();
        }
        call_user_func_array(array($segments[0], $segments[1]), $depends);
    };
    $route = (require 'config.php')['site']['prefix'] . $route;
    switch ($method) {
    case 'get':
        Macaw::get($route, $func);
        break;
    case 'post':
        Macaw::post($route, $func);
        break;
    case 'put':
        Macaw::put($route, $func);
        break;
    case 'patch':
        Macaw::patch($route, $func);
        break;
    case 'delete':
        Macaw::delete($route, $func);
        break;
    }
}

addRouter('post', '/api/user/login', 'UserController@login');
addRouter('get', '/api/user/logout', 'UserController@logout');
addRouter('get', '/api/user/current', 'UserController@current');
addRouter('post', '/api/user/complete/nuaa', 'UserController@completeNuaa');
addRouter('post', '/api/user/complete/discuz', 'UserController@completeDiscuz');

Macaw::dispatch();
