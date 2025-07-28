--TEST--
Request XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

var_dump(XML_RPC2_Backend_Php_Response::encode([1, true, 'a string']));
?>
--EXPECT--
string(249) "<?xml version="1.0" encoding="utf-8"?>
<methodResponse><params><param><value><array><data><value><int>1</int></value><value><boolean>1</boolean></value><value><string>a string</string></value></data></array></value></param></params></methodResponse>"
