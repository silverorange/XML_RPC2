--TEST--
Integer XML-RPC encoding (Php Backend)
--SKIPIF--
<?php
if (PHP_INT_SIZE < 8) {
    echo 'Skip: Integer64 is only available on 64bit systems';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$integer = new XML_RPC2_Backend_Php_Value_Integer64(34359738368);
var_dump($integer->encode());
?>
--EXPECT--
string(20) "<i8>34359738368</i8>"
