<?php

class DataBase extends Medoo {
    public function __construct() {
        parent::__construct((require 'config.php')['db']);
    }
}
