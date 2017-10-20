--TEST--
Nil XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$null = new XML_RPC2_Backend_Php_Value_Nil();
var_dump($null->encode());
?>
--EXPECT--
string(11) "<nil></nil>"
