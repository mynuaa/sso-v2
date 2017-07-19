<?php

class PathVariable {
    private static $patterns = [
        ':any' => '[^/]+',
        ':num' => '[0-9]+',
        ':all' => '.*'
    ];
    private $params = [];
    public function __construct() {
        // copy fron Macaw
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        $searches = array_keys(static::$patterns);
        $replaces = array_values(static::$patterns);
        $pos = 0;
        foreach (Macaw::$routes as $route) {
            if (strpos($route, ':') !== false) {
                $route = str_replace($searches, $replaces, $route);
            }
            if (preg_match('#^' . $route . '$#', $uri, $matched)) {
                if (Macaw::$methods[$pos] == $method || self::$methods[$pos] == 'ANY') {
                    array_shift($matched);
                    $this->params = $matched;
                }
            }
            $pos++;
        }
    }
    public function get($key) {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        } else {
            die('PathVariable "' . $key . '" not found.');
        }
    }
}
