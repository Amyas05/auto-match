<?php
require_once __DIR__ . '/app/bootstrap.php';
$config = require __DIR__ . '/config/database.php';

use App\Controllers\AuthController;

(new AuthController())->register();
?>