<?php

require 'vendor/autoload.php';
require 'internals/FileSystemRouter.php';

$router = new FileSystemRouter(__DIR__ . '/routes');
$router->run();
