--TEST--
PHP Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache on by default 2)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    print "Skip no cURL extension available";
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = array(
    'debug' => false,
    'backend' => 'Php',
    'prefix' => 'validator1.',
    'cacheOptions' => array(
        'cacheDir' => sys_get_temp_dir() . '/',
        'lifetime' => 60,
        'cacheByDefault' => true,
        'notCachedMethods' => array('foo', 'bar', 'easyStructTest', 'foobar')
    ),
    'cacheDebug' => true
);

$client = XML_RPC2_CachedClient::create('https://gggeek.altervista.org/sw/xmlrpc/demo/server/server.php', $options);
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
CACHE DEBUG : the called method is listed in _notCachedMethods => no cache !
int(19)
CACHE DEBUG : the called method is listed in _notCachedMethods => no cache !
int(19)
