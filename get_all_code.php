<?php
function getAllPhpFiles($dir, &$files = []) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php' && strpos($file->getPathname(), 'PHPMailer') === false) {
            $files[] = $file->getPathname();
        }
    }
    return $files;
}

$files = getAllPhpFiles('.');

foreach ($files as $file) {
    echo "--- Start of $file ---\n";
    echo file_get_contents($file);
    echo "\n--- End of $file ---\n\n";
}
?>
