--TEST--
Datetime XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

date_default_timezone_set('UTC');

$result = XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><dateTime.iso8601>2005</dateTime.iso8601></value>'))->getNativeValue();
var_dump($result->xmlrpc_type);
var_dump($result->scalar);
var_dump($result->timestamp);

?>
--EXPECT--
string(8) "datetime"
string(4) "2005"
int(1104537600)
