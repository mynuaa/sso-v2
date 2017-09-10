<?php

namespace PhalApi;

use PhalApi\Request as PhalApi_Request;

/**
 * 让 PhalApi 可以使用 JSON 作为参数
 * @author Rex Zeng <rex@rexskz.info> 2017-09-10
 */
class JSON_Request extends PhalApi_Request {
    public function __construct() {
        $json = json_decode(file_get_contents('php://input'), true);
        // Header 中可能不是 JSON
        if (!$json) {
            $json = [];
        }
        $data = array_merge($json, $_GET, $_POST);
        parent::__construct($data);
    }
}
