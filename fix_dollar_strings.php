<?php

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
$count = 0;

foreach ($dir as $file) {
    if ($file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        $newContent = str_replace("'$'.", "'₹'.", $content);
        $newContent = str_replace("'$'", "'₹'", $newContent);
        $newContent = str_replace(" (USD)", " (INR)", $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated remaining dollars: $path\n";
            $count++;
        }
    }
}
echo "Total fixes: $count\n";
