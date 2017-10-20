--TEST--
Datetime XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

date_default_timezone_set('UTC');

$time = new XML_RPC2_Backend_Php_Value_Datetime(853438830.45);
var_dump($time->encode());
?>
--EXPECT--
string(54) "<dateTime.iso8601>19970116T18:20:30</dateTime.iso8601>"
