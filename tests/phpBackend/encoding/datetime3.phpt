--TEST--
Datetime XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

date_default_timezone_set('UTC');

$time = new XML_RPC2_Backend_Php_Value_Datetime('2001');
var_dump($time->encode());
?>
--EXPECT--
string(41) "<dateTime.iso8601>2001</dateTime.iso8601>"
