--TEST--
Integer XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

printf("Native value: %s\n", XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><i4>7</i4></value>'))->getNativeValue());
?>
--EXPECT--
Native value: 7
