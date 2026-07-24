<?php
$images = [
    'gcash.png' => 'https://1000logos.net/wp-content/uploads/2023/05/GCash-Logo.png',
    'maya.png' => 'https://1000logos.net/wp-content/uploads/2023/05/Maya-Logo.png'
];

$dir = __DIR__ . '/public/images';
if (!is_dir($dir)) {
    mkdir($dir, 0755, true);
}

foreach ($images as $name => $url) {
    $content = @file_get_contents($url);
    if ($content) {
        file_put_contents($dir . '/' . $name, $content);
        echo "Downloaded: $name\n";
    } else {
        echo "Failed to download: $name\n";
    }
}
