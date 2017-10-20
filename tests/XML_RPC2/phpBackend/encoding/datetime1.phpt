--TEST--
Datetime XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

$time = new XML_RPC2_Backend_Php_Value_Datetime('1997-01-16T19:20:30.45+01:00');
var_dump($time->encode());
?>
--EXPECT--
string(65) "<dateTime.iso8601>1997-01-16T19:20:30.45+01:00</dateTime.iso8601>"
