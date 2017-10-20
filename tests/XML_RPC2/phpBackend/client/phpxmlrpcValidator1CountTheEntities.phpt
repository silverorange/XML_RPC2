--TEST--
PHP Backend XML-RPC client against phpxmlrpc validator1 (countTheEntities)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    print "Skip no CURI extension available";
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

$options = array(
    'debug' => false,
    'backend' => 'Php',
    'prefix' => 'validator1.'
);
$client = XML_RPC2_Client::create('http://phpxmlrpc.sourceforge.net/server.php', $options);
$string = "foo <<< bar '> && '' #fo>o \" bar";
$result = $client->countTheEntities($string);
var_dump($result['ctLeftAngleBrackets']);
var_dump($result['ctRightAngleBrackets']);
var_dump($result['ctAmpersands']);
var_dump($result['ctApostrophes']);
var_dump($result['ctQuotes']);


?>
--EXPECT--
int(3)
int(2)
int(2)
int(3)
int(1)
