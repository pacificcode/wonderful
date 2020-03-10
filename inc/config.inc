<?php
session_start();
ini_set("display_errors","On5");
date_default_timezone_set('UTC');

define("TIME_BEGIN", microtime(true));

define("DB_HOST", "127.0.0.1");
define("DB_USER", "");
define("DB_PASSWD", "");
define("DB_DATABASE", "");

spl_autoload_register(function ($class) {
    require_once 'class/class.' . $class . '.php';
});


?>