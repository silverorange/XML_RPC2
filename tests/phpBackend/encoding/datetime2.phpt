--TEST--
Datetime XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$time = new XML_RPC2_Backend_Php_Value_Datetime('1997-01-16T19:20:30.45Z');
var_dump($time->encode());
?>
--EXPECT--
string(60) "<dateTime.iso8601>1997-01-16T19:20:30.45Z</dateTime.iso8601>"
