--TEST--
Array XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$array = new XML_RPC2_Backend_Php_Value_Array([1, true, 'a string']);
var_dump($array->encode());
?>
--EXPECT--
string(130) "<array><data><value><int>1</int></value><value><boolean>1</boolean></value><value><string>a string</string></value></data></array>"
