<?php
require_once __DIR__ . '/vendor/autoload.php';

echo 'Autoloader test: ';
var_dump(class_exists('Mpdf\Mpdf'));
