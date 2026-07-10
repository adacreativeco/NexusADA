<?php
$dir = __DIR__.'/../storage/fonts';
if (!file_exists($dir)) {
    mkdir($dir, 0777, true);
    chmod($dir, 0777);
    echo "Directory created: $dir\n";
} else {
    echo "Directory already exists: $dir\n";
}
@unlink(__FILE__);
