<?php
$j = json_decode(file_get_contents('composer.json'), true);
var_dump(isset($j['autoload']['psr-4']['Modules\\\\']));

