--TEST--
PHP Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache off by default 1)
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
    'backend' => 'Php',
    'prefix' => 'validator1.',
    'cacheOptions' => array(
        'cacheDir' => sys_get_temp_dir() . '/',
        'lifetime' => 60,
        'cacheByDefault' => false
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
$client->dropCacheFile___('easyStructTest', $arg);

?>
--EXPECT--
CACHE DEBUG : cache is not on by default and the called method is not listed in _cachedMethods => no cache !
int(19)
CACHE DEBUG : cache is not on by default and the called method is not listed in _cachedMethods => no cache !
int(19)
