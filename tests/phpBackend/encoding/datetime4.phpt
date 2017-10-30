--TEST--
Datetime XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

date_default_timezone_set('UTC');

$time = new XML_RPC2_Backend_Php_Value_Datetime('1997-01-16');
var_dump($time->encode());
?>
--EXPECT--
string(47) "<dateTime.iso8601>1997-01-16</dateTime.iso8601>"
