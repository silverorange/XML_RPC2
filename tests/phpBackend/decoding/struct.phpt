--TEST--
Struct XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

var_dump(XML_RPC2_Backend_Php_Value::createFromDecode(simplexml_load_string('<value><struct><member><name>a</name><value><int>1</int></value></member><member><name>b</name><value><boolean>1</boolean></value></member><member><name>c</name><value><string>a string</string></value></member></struct></value>'))->getNativeValue());
?>
--EXPECT--
array(3) {
  ["a"]=>
  int(1)
  ["b"]=>
  bool(true)
  ["c"]=>
  string(8) "a string"
}
