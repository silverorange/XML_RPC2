--TEST--
Struct XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$struct = new XML_RPC2_Backend_Php_Value_Struct(array('a' => 1, 'b' => true, 'c' => 'a string'));
var_dump($struct->encode());
?>
--EXPECT--
string(212) "<struct><member><name>a</name><value><int>1</int></value></member><member><name>b</name><value><boolean>1</boolean></value></member><member><name>c</name><value><string>a string</string></value></member></struct>"
