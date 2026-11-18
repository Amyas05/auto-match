<?php

require_once __DIR__ . '/Core/Autoloader.php';
Autoloader::register();

use App\Core\Session;

Session::start();

$config = require dirname(__DIR__) . '/config/database.php';

return $config;
