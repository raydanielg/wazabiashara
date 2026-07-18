<?php
/**
 * Root index.php — redirects all traffic to /public
 * Wazabiashara — Laravel PWA
 */

$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve static files from /public if they exist
$publicPath = __DIR__ . '/public' . $uri;
if ($uri !== '/' && file_exists($publicPath) && !is_dir($publicPath)) {
    return false;
}

// Load Laravel's public/index.php
require __DIR__ . '/public/index.php';
