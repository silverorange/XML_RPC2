--TEST--
XMLRPCext Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache on by default 4)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    echo 'Skip no cURL extension available';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = [
    'debug'        => false,
    'backend'      => 'Php',
    'prefix'       => 'validator1.',
    'cacheOptions' => [
        'cacheDir'       => sys_get_temp_dir() . '/',
        'lifetime'       => 60,
        'cacheByDefault' => true,
        'cachedMethods'  => [
            'foo'            => 30,
            'bar'            => 10,
            'easyStructTest' => -1,
            'foobar'         => 60,
        ],
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
$client->dropCacheFile___('easyStructTest', [$arg]);

?>
--EXPECT--
CACHE DEBUG : called method has a -1 lifetime value => no cache !
int(19)
CACHE DEBUG : called method has a -1 lifetime value => no cache !
int(19)
