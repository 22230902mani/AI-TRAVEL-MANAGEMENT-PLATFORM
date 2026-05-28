<?php

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
$count = 0;

foreach ($dir as $file) {
    if ($file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // Replace fa-dollar-sign with fa-indian-rupee-sign
        $newContent = str_replace('fa-dollar-sign', 'fa-indian-rupee-sign', $content);
        
        // Fix Chart.js tooltip label specifically
        $newContent = str_replace("'$' + v.toLocaleString()", "'₹' + v.toLocaleString()", $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated icon/chart: $path\n";
            $count++;
        }
    }
}
echo "Total fixes: $count\n";
