--TEST--
Response XML-RPC decoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

var_dump(XML_RPC2_Backend_Php_Response::decode(simplexml_load_string('<?xml version="1.0"?><methodResponse><params><param><value><string>South Dakota</string></value></param></params></methodResponse>')));
?>
--EXPECT--
string(12) "South Dakota"
