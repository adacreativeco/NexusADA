<?php
$files = [
    __DIR__.'/downloads/Nexus-ADA-1.0.0-setup.exe',
    __DIR__.'/releases/NexusADA-v2.0-Setup.exe',
    __DIR__.'/test_outbound.php',
    __DIR__.'/list_fonts.php',
    __DIR__.'/create_fonts_dir.php',
    __DIR__.'/db_diagnostics.php',
    __DIR__.'/clear_bootstrap_cache.php',
];
foreach ($files as $file) {
    if (file_exists($file)) {
        if (unlink($file)) {
            echo "Deleted file: $file\n";
        } else {
            echo "Failed to delete file: $file\n";
        }
    } else {
        echo "File does not exist: $file\n";
    }
}

// Also delete directories if empty
$dirs = [
    __DIR__.'/downloads',
    __DIR__.'/releases',
];
foreach ($dirs as $dir) {
    if (is_dir($dir)) {
        $files_in_dir = array_diff(scandir($dir), array('.', '..'));
        if (empty($files_in_dir)) {
            if (rmdir($dir)) {
                echo "Deleted empty dir: $dir\n";
            }
        }
    }
}

@unlink(__FILE__);
