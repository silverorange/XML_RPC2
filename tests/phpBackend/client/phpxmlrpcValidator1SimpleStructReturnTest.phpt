--TEST--
PHP Backend XML-RPC client against phpxmlrpc validator1 (simpleStructReturnTest)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    echo 'Skip no CURI extension available';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = [
    'debug'   => false,
    'backend' => 'Php',
    'prefix'  => 'validator1.',
];
$client = XML_RPC2_Client::create('https://gggeek.altervista.org/sw/xmlrpc/demo/server/server.php', $options);
$result = $client->simpleStructReturnTest(13);
var_dump($result['times10']);
var_dump($result['times100']);
var_dump($result['times1000']);

?>
--EXPECT--
int(130)
int(1300)
int(13000)
