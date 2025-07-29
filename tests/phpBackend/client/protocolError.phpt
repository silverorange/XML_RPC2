--TEST--
PHP Backend XML-RPC client with transport error
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    echo 'Skip no CURI extension available';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

XML_RPC2_Backend::setBackend('php');
$client = XML_RPC2_Client::create('http://rpc.example.com:1000/', '', null);

try {
    $client->invalidMethod('World');
} catch (XML_RPC2_Exception_Curl $e) {
    var_dump($e->getMessage());
}
?>
--EXPECTREGEX--
string\(.*\) \"HTTP_Request2_ConnectionException.*
