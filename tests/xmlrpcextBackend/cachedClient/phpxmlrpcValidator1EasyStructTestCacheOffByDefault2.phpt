--TEST--
XMLRPCext Backend XML-RPC cachedClient against phpxmlrpc validator1 (easyStructTest with cache off by default 2)
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

$dir = sys_get_temp_dir() . '/cache_' . rand().'/';
@mkdir($dir);
$options = array(
    'debug' => false,
    'backend' => 'Xmlrpcext',
    'prefix' => 'validator1.',
    'cacheOptions' => array(
        'cacheDir' => $dir,
        'lifetime' => 60,
        'cacheByDefault' => false,
        'cachedMethods' => array('foo', 'bar', 'easyStructTest', 'foo2', 'bar2')
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
$result = $client->easyStructTest($arg);
var_dump($result);
$client->dropCacheFile___('easyStructTest', array($arg));
@rmdir($dir);

?>
--EXPECT--
CACHE DEBUG : cache is not hit !
int(19)
CACHE DEBUG : cache is hit !
int(19)
CACHE DEBUG : cache is hit !
int(19)
