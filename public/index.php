<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Wasmer PHP 8.3 Polyfill for Symfony 8
if (!function_exists('request_parse_body')) {
    function request_parse_body(array $options = null): array {
        return [$_POST, $_FILES];
    }
}
if (!class_exists('RequestParseBodyException')) {
    class RequestParseBodyException extends \Exception {}
}

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
