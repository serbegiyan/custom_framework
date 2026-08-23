<?php

require __DIR__ . '/../vendor/autoload.php';

$_ENV['DB_NAME'] = 'custom_framework_test';
$_SERVER['DB_NAME'] = 'custom_framework_test';

require __DIR__ . '/../bin/migrate.php';
