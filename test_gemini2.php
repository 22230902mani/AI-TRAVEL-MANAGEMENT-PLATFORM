<?php
$data = json_decode(file_get_contents('https://generativelanguage.googleapis.com/v1beta/models?key=AIzaSyDWdnkeJ24qaJzzPhYYnLnr1bueZtzsl40'), true);
foreach ($data['models'] as $m) {
    if (in_array('generateContent', $m['supportedGenerationMethods'])) {
        echo $m['name'] . "\n";
    }
}
