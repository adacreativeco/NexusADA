<?php
$files = [
    __DIR__.'/../bootstrap/cache/packages.php',
    __DIR__.'/../bootstrap/cache/services.php',
    __DIR__.'/../bootstrap/cache/config.php',
    __DIR__.'/../bootstrap/cache/routes.php',
];
foreach ($files as $file) {
    if (file_exists($file)) {
        unlink($file);
        echo "Deleted: $file\n";
    }
}
@unlink(__FILE__);
