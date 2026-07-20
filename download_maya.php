<?php
$url = 'https://download.logo.wine/logo/PayMaya/PayMaya-Logo.wine.png';
$content = @file_get_contents($url);
if ($content) {
    file_put_contents(__DIR__ . '/public/images/maya.png', $content);
    echo "Maya downloaded.\n";
} else {
    echo "Failed.\n";
}
