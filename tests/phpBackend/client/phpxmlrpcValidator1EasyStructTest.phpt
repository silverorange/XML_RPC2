--TEST--
PHP Backend XML-RPC client against phpxmlrpc validator1 (easyStructTest)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    print "Skip no CURI extension available";
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = array(
    'debug' => false,
    'backend' => 'Php',
    'prefix' => 'validator1.'
);
$client = XML_RPC2_Client::create('http://phpxmlrpc.sourceforge.net/server.php', $options);
$arg = array(
    'moe' => 5,
    'larry' => 6,
    'curly' => 8
);
$result = $client->easyStructTest($arg);
var_dump($result);

?>
--EXPECT--
int(19)
