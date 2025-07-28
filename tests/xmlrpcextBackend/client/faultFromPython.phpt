--TEST--
XMLRPCext Backend XML-RPC client against python server returning fault response
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    echo 'Skip XMLRPC extension unavailable';
}

// $handle = @fopen("http://python.xmlrpc2test.sergiocarvalho.com:8765", "r");
// if (!$handle) {
echo 'skip : The python XMLRPC server is not available !';
// } else {
//    fclose($handle);
// }
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

XML_RPC2_Backend::setBackend('xmlrpcext');
$client = XML_RPC2_Client::create('http://python.xmlrpc2test.sergiocarvalho.com:8765', '', null);

try {
    $client->invalidMethod('World');
} catch (XML_RPC2_Exception_Fault $e) {
    var_dump($e->getMessage());
}
?>
--EXPECT--
string(60) "exceptions.Exception:method "invalidMethod" is not supported"
