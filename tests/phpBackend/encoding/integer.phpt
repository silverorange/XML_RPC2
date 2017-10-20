--TEST--
Integer XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$integer = new XML_RPC2_Backend_Php_Value_Integer(53);
var_dump($integer->encode());
?>
--EXPECT--
string(13) "<int>53</int>"
