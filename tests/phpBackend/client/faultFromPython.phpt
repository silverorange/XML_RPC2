--TEST--
PHP Backend XML-RPC client against python server returning fault response
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    echo 'Skip no CURI extension available';
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

XML_RPC2_Backend::setBackend('php');
$client = XML_RPC2_Client::create('http://python.xmlrpc2test.sergiocarvalho.com:8765', '', null);

try {
    $client->invalidMethod('World');
} catch (XML_RPC2_Exception_Fault $e) {
    var_dump($e->getMessage());
}
?>
--EXPECT--
string(60) "exceptions.Exception:method "invalidMethod" is not supported"
