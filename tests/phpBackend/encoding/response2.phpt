--TEST--
Request XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

var_dump(XML_RPC2_Backend_Php_Response::encodeFault(2,'A fault string'));
?>
--EXPECT--
string(272) "<?xml version="1.0" encoding="utf-8"?>
<methodResponse><fault><value><struct><member><name>faultCode</name><value><int>2</int></value></member><member><name>faultString</name><value><string>A fault string</string></value></member></struct></value></fault></methodResponse>"
