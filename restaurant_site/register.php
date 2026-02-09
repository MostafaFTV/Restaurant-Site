<?php

require_once 'includes/db.php';
require_once 'controllers/AuthController.php';

$auth = new AuthController($pdo);
$auth->register();
?>