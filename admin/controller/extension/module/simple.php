<?php

$php_version = PHP_MAJOR_VERSION . PHP_MINOR_VERSION;
var_dump($php_version);
if (!in_array($php_version, array('82', '83', '84'))) {
    exit('Simple: PHP ' . PHP_VERSION . ' is not supported, PHP 8.2 - 8.4 is required');
}
require_once(DIR_SYSTEM . '/library/simple/php/simple_admin_' . $php_version . '.php');