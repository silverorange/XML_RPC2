--TEST--
XMLRPCext backend test setting explicit type for value
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    echo 'Skip XMLRPC extension unavailable';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

XML_RPC2_Backend::setBackend('xmlrpcext');
var_dump(XML_RPC2_Value::createFromNative('Hello World', 'base64'));
?>
--EXPECT--
object(stdClass)#3 (2) {
  ["scalar"]=>
  string(11) "Hello World"
  ["xmlrpc_type"]=>
  string(6) "base64"
}
