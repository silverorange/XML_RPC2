--TEST--
Integer XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

$value = XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><nil/></value>'))->getNativeValue();

printf("Native value: %s\n", is_null($value) ? 'null' : 'not null');
?>
--EXPECT--
Native value: null
