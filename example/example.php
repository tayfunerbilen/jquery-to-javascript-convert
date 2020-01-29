<?php

require dirname(__DIR__) . '/vendor/autoload.php';

$js = file_get_contents(__DIR__ . '/test.js');

echo \Erbilen\JqueryToJS::convert($js);
