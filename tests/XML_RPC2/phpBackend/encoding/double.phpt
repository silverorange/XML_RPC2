--TEST--
Double XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

$double = new XML_RPC2_Backend_Php_Value_Double(0);
var_dump($double->encode());
$double = new XML_RPC2_Backend_Php_Value_Double(123.79);
var_dump($double->encode());
?>
--EXPECT--
string(18) "<double>0</double>"
string(23) "<double>123.79</double>"
