--TEST--
XMLRPCext Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache off by default 1)
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
    'debug'        => false,
    'backend'      => 'Xmlrpcext',
    'prefix'       => 'validator1.',
    'cacheOptions' => [
        'cacheDir'       => sys_get_temp_dir() . '/',
        'lifetime'       => 60,
        'cacheByDefault' => false,
    ],
    'cacheDebug' => true,
];

$client = XML_RPC2_CachedClient::create('https://gggeek.altervista.org/sw/xmlrpc/demo/server/server.php', $options);
$arg = [
    'moe'   => 5,
    'larry' => 6,
    'curly' => 8,
];
$result = $client->easyStructTest($arg);
var_dump($result);
$result = $client->easyStructTest($arg);
var_dump($result);
$client->dropCacheFile___('easyStructTest', $arg);

?>
--EXPECT--
CACHE DEBUG : cache is not on by default and the called method is not listed in _cachedMethods => no cache !
int(19)
CACHE DEBUG : cache is not on by default and the called method is not listed in _cachedMethods => no cache !
int(19)
