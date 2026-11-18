<?php
require_once __DIR__ . '/app/bootstrap.php';

use App\Controllers\DashboardController;

(new DashboardController())->publish();
?>