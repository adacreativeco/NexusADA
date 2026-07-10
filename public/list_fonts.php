<?php
$dir = __DIR__.'/../storage/fonts';
if (file_exists($dir)) {
    $files = scandir($dir);
    echo json_encode($files, JSON_PRETTY_PRINT);
} else {
    echo "Directory does not exist";
}
@unlink(__FILE__);
