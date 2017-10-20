--TEST--
Array XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

var_dump(XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<?xml version="1.0"?><value><array><data><value><int>1</int></value><value><boolean>1</boolean></value><value><string>a</string></value></data></array></value>'))->getNativeValue());
?>
--EXPECT--
array(3) {
  [0]=>
  int(1)
  [1]=>
  bool(true)
  [2]=>
  string(1) "a"
}
