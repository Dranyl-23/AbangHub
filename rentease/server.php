<?php

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

// Vercel serverless environment overrides for read-only filesystem
$_ENV['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_SERVER['VIEW_COMPILED_PATH'] = '/tmp/storage/framework/views';
$_ENV['LOG_CHANNEL'] = 'stderr';
$_SERVER['LOG_CHANNEL'] = 'stderr';
$_ENV['SESSION_DRIVER'] = 'cookie';
$_SERVER['SESSION_DRIVER'] = 'cookie';
$_ENV['APP_DEBUG'] = 'true';
$_SERVER['APP_DEBUG'] = 'true';

// Vercel cache paths
$cachePath = '/tmp/storage/bootstrap/cache';
$_ENV['APP_CONFIG_CACHE'] = $cachePath . '/config.php';
$_SERVER['APP_CONFIG_CACHE'] = $cachePath . '/config.php';
$_ENV['APP_EVENTS_CACHE'] = $cachePath . '/events.php';
$_SERVER['APP_EVENTS_CACHE'] = $cachePath . '/events.php';
$_ENV['APP_PACKAGES_CACHE'] = $cachePath . '/packages.php';
$_SERVER['APP_PACKAGES_CACHE'] = $cachePath . '/packages.php';
$_ENV['APP_ROUTES_CACHE'] = $cachePath . '/routes.php';
$_SERVER['APP_ROUTES_CACHE'] = $cachePath . '/routes.php';
$_ENV['APP_SERVICES_CACHE'] = $cachePath . '/services.php';
$_SERVER['APP_SERVICES_CACHE'] = $cachePath . '/services.php';

// Create temp directories if they don't exist
@mkdir('/tmp/storage/framework/views', 0777, true);
@mkdir('/tmp/storage/framework/cache/data', 0777, true);
@mkdir('/tmp/storage/logs', 0777, true);
@mkdir('/tmp/storage/framework/sessions', 0777, true);
@mkdir($cachePath, 0777, true);

require __DIR__ . '/public/index.php';
