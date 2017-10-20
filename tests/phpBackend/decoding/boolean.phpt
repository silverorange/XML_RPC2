--TEST--
Boolean XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

printf("Native value: %s\n", XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><boolean>0</boolean></value>'))->getNativeValue() ? 'true' : 'false');
printf("Native value: %s\n", XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><boolean>1</boolean></value>'))->getNativeValue() ? 'true' : 'false');
?>
--EXPECT--
Native value: false
Native value: true
