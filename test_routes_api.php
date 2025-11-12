<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';

$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Route;

echo "=== Checking API Routes ===\n\n";

$routes = Route::getRoutes();

$apiRoutes = [];
foreach ($routes as $route) {
    if (strpos($route->uri, 'api/unread') !== false) {
        $apiRoutes[] = $route;
    }
}

echo "Found " . count($apiRoutes) . " routes with 'api/unread':\n\n";

foreach ($apiRoutes as $route) {
    echo "Route: " . $route->uri . "\n";
    echo "  Name: " . $route->getName() . "\n";
    echo "  Methods: " . implode(',', $route->methods) . "\n";
    echo "  Action: " . $route->getActionName() . "\n";
    echo "  Parameters: " . implode(',', $route->parameterNames()) . "\n\n";
}

// Test building URLs for each role
echo "=== Testing URL Building ===\n\n";

$roles = ['ppk', 'verifikator', 'pokjapemilihan'];
$pengajuanId = 1;

foreach ($roles as $role) {
    $routeName = $role . '_api.unread.count';
    try {
        $url = route($routeName, ['id' => $pengajuanId]);
        echo "Role: " . $role . "\n";
        echo "  Route Name: " . $routeName . "\n";
        echo "  URL: " . $url . "\n";
        echo "  Expected: /" . $role . "/api/unread-count/" . $pengajuanId . "\n";
        echo "  Match: " . (strpos($url, "/" . $role . "/api/unread-count/" . $pengajuanId) !== false ? "YES ✓" : "NO ✗") . "\n\n";
    } catch (\Exception $e) {
        echo "Role: " . $role . "\n";
        echo "  ERROR: " . $e->getMessage() . "\n\n";
    }
}

// Test middleware
echo "=== Testing Middleware ===\n\n";

$middlewareGroups = [
    'ppk',
    'verifikator',
    'pokjapemilihan'
];

foreach ($middlewareGroups as $prefix) {
    echo "Prefix: " . $prefix . "\n";
    $routesForPrefix = [];
    foreach ($routes as $route) {
        if (strpos($route->uri, $prefix) === 0) {
            $routesForPrefix[] = $route->uri;
        }
    }
    echo "  Total routes: " . count($routesForPrefix) . "\n";
    echo "  Sample routes:\n";
    foreach (array_slice($routesForPrefix, 0, 3) as $uri) {
        echo "    - " . $uri . "\n";
    }
    echo "\n";
}

