<?php

require_once dirname(__DIR__) . '/app/bootstrap.php';
$config = require dirname(__DIR__) . '/config/database.php';

use App\Controllers\CatalogueController;

$controller = new CatalogueController();
$controller->index($config);
