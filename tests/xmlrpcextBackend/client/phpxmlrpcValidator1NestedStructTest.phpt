--TEST--
XMLRPCext Backend XML-RPC client against phpxmlrpc validator1 (nestedStructTest)
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    echo 'Skip XMLRPC extension unavailable';
}
if (!function_exists('curl_init')) {
    echo 'Skip CURL extension unavailable';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = [
    'debug'   => false,
    'backend' => 'Xmlrpcext',
    'prefix'  => 'validator1.',
];
$client = XML_RPC2_Client::create('https://gggeek.altervista.org/sw/xmlrpc/demo/server/server.php', $options);

$year1999 = [
    '04' => [],
];
$year2001 = $year1999;
$year2000 = $year1999;
$year2000['04']['01'] = [
    'moe'   => 12,
    'larry' => 14,
    'curly' => 9,
];

$index1999 = '1999 ';
$index2000 = '2000 ';
$index2001 = '2001 ';
$cal = [];
$cal['1999'] = $year1999;
$cal['2000'] = $year2000;
$cal['2001'] = $year2001;

$cal = XML_RPC2_Value::createFromNative($cal, 'struct');
$result = $client->nestedStructTest($cal);
var_dump($result);

?>
--EXPECT--
int(35)
