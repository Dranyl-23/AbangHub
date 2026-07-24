<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$user = App\Models\User::where('user_type', 'landlord')->first();
$request = Illuminate\Http\Request::create('/api/dashboard', 'GET');
$request->setUserResolver(function() use ($user) { return $user; });

$controller = new App\Http\Controllers\Api\DashboardController();
try {
    $response = $controller->index($request);
    echo $response->getContent();
} catch (\Exception $e) {
    echo $e->getMessage();
}
