--TEST--
Double XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

printf("Native value: %s\n", XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><double>1.25</double></value>'))->getNativeValue());
?>
--EXPECT--
Native value: 1.25
