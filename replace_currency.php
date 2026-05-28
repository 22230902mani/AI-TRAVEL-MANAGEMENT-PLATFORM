<?php

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/resources/views'));
$count = 0;

foreach ($dir as $file) {
    if ($file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // Match $ followed directly by {{
        // Match $ followed directly by a number
        // Match $ followed by a space and a number
        $newContent = preg_replace('/\$(\{\{)/', '₹$1', $content);
        $newContent = preg_replace('/\$([0-9])/', '₹$1', $newContent);
        $newContent = preg_replace('/\$ ([0-9])/', '₹ $1', $newContent);

        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated: $path\n";
            $count++;
        }
    }
}

// Also check controllers for hardcoded '$' inside strings that look like currency
$dirControllers = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/app/Http/Controllers'));
foreach ($dirControllers as $file) {
    if ($file->getExtension() === 'php') {
        $path = $file->getRealPath();
        $content = file_get_contents($path);
        
        // This is a bit riskier in PHP code, but we can look for " $" or "$ " or "$'" or ".'$'"
        // Let's specifically look for "'$'" which is used in AdminDashboardController
        $newContent = str_replace("'$'", "'₹'", $content);
        $newContent = str_replace('"$', '"₹', $newContent);
        
        if ($newContent !== $content) {
            file_put_contents($path, $newContent);
            echo "Updated Controller: $path\n";
            $count++;
        }
    }
}

echo "Total files updated: $count\n";
