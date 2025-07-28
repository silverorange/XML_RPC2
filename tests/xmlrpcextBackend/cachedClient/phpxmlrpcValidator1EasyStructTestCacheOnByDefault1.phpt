--TEST--
XMLRPCext Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache on by default 1)
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

$dir = sys_get_temp_dir() . '/cache_' . rand() . '/';
@mkdir($dir);
$options = [
    'debug'        => false,
    'backend'      => 'Xmlrpcext',
    'prefix'       => 'validator1.',
    'cacheOptions' => [
        'cacheDir'       => $dir,
        'lifetime'       => 60,
        'cacheByDefault' => true,
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
$result = $client->easyStructTest($arg);
var_dump($result);
$client->dropCacheFile___('easyStructTest', [$arg]);
@rmdir($dir);

?>
--EXPECT--
CACHE DEBUG : cache is not hit !
int(19)
CACHE DEBUG : cache is hit !
int(19)
CACHE DEBUG : cache is hit !
int(19)
