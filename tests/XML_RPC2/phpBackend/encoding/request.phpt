--TEST--
Request XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

$request = new XML_RPC2_Backend_Php_Request('foo.bar');
$request->addParameter('a string');
$request->addParameter(125);
$request->addParameter(125.2);
$request->addParameter(new XML_RPC2_Backend_Php_Value_Datetime('2005-01-03'));
$request->addParameter(true);
$request->addParameter(false);
var_dump($request->encode());
?>
--EXPECT--
string(441) "<?xml version="1.0" encoding="utf-8"?>
<methodCall><methodName>foo.bar</methodName><params><param><value><string>a string</string></value></param><param><value><int>125</int></value></param><param><value><double>125.2</double></value></param><param><value><dateTime.iso8601>2005-01-03</dateTime.iso8601></value></param><param><value><boolean>1</boolean></value></param><param><value><boolean>0</boolean></value></param></params></methodCall>"
