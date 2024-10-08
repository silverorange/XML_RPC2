--TEST--
XMLRPCext Backend XML-RPC client against phpxmlrpc validator1 (moderateSizeArrayCheck)
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    print "Skip XMLRPC extension unavailable";
}
if (!function_exists('curl_init')) {
    print "Skip CURL extension unavailable";
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = array(
    'debug' => false,
    'backend' => 'Xmlrpcext',
    'prefix' => 'validator1.'
);
$client = XML_RPC2_Client::create('https://gggeek.altervista.org/sw/xmlrpc/demo/server/server.php', $options);
$tmp = array('foo');
for ($i = 0 ; $i<150 ; $i++) {
    $tmp[] = "bla bla bla";
}
$tmp[] = "bar";
$result = $client->moderateSizeArrayCheck($tmp);
echo($result . "\n");

?>
--EXPECT--
foobar
