--TEST--
Base64 XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$string = new XML_RPC2_Backend_Php_Value_Base64('The quick brown fox jumped over the lazy dog');
var_dump($string->encode());
$string = new XML_RPC2_Backend_Php_Value_Base64('The <quick> brown fox jumped over the lazy dog');
var_dump($string->encode());
?>
--EXPECT--
string(77) "<base64>VGhlIHF1aWNrIGJyb3duIGZveCBqdW1wZWQgb3ZlciB0aGUgbGF6eSBkb2c=</base64>"
string(81) "<base64>VGhlIDxxdWljaz4gYnJvd24gZm94IGp1bXBlZCBvdmVyIHRoZSBsYXp5IGRvZw==</base64>"
