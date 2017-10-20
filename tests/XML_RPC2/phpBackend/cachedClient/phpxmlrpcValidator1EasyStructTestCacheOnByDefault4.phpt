--TEST--
XMLRPCext Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache on by default 4)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    print "Skip no cURL extension available";
}
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

$options = array(
    'debug' => false,
    'backend' => 'Xmlrpcext',
    'prefix' => 'validator1.',
    'cacheOptions' => array(
        'cacheDir' => sys_get_temp_dir() . '/',
        'lifetime' => 60,
        'cacheByDefault' => true,
        'cachedMethods' => array(
            'foo' => 30,
            'bar' => 10,
            'easyStructTest' => -1,
            'foobar' => 60
        )
    ),
    'cacheDebug' => true
);

$client = XML_RPC2_CachedClient::create('http://phpxmlrpc.sourceforge.net/server.php', $options);
$arg = array(
    'moe' => 5,
    'larry' => 6,
    'curly' => 8
);
$result = $client->easyStructTest($arg);
var_dump($result);
$result = $client->easyStructTest($arg);
var_dump($result);
$client->dropCacheFile___('easyStructTest', array($arg));

?>
--EXPECT--
CACHE DEBUG : called method has a -1 lifetime value => no cache !
int(19)
CACHE DEBUG : called method has a -1 lifetime value => no cache !
int(19)
