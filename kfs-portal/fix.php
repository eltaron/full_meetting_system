<?php
$j = json_decode(file_get_contents('composer.json'), true);
$j['autoload']['psr-4']['Modules\\\\'] = 'Modules/';
file_put_contents('composer.json', json_encode($j, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES).PHP_EOL);

