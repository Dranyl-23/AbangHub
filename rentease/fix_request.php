<?php
$file = 'vendor/symfony/http-foundation/Request.php';
$content = file_get_contents($file);
$content = preg_replace('/ \{[\s\n\r]*set \{.*?\n\s*\}[\s\n\r]*\}/s', ';', $content);
file_put_contents($file, $content);
echo "Fixed";
