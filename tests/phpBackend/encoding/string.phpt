--TEST--
String XML-RPC encoding (Php Backend)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$string = new XML_RPC2_Backend_Php_Value_String('The quick brown fox jumped over the lazy dog');
var_dump($string->encode());
$string = new XML_RPC2_Backend_Php_Value_String('The <quick> brown fox jumped over the lazy dog');
var_dump($string->encode());
?>
--EXPECT--
string(61) "<string>The quick brown fox jumped over the lazy dog</string>"
string(69) "<string>The &lt;quick&gt; brown fox jumped over the lazy dog</string>"
