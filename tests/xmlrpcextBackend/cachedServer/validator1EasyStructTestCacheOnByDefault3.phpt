--TEST--
XMLRPCext Backend XML-RPC cachedServer Validator1 test (easyStructTest with cache on by default 3)
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    echo 'Skip XMLRPC extension unavailable';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

class TestServer
{
    /**
     * test function.
     *
     * see http://www.xmlrpc.com/validator1Docs
     *
     * @param array $struct a struct
     *
     * @return int result
     *
     * @xmlrpc.caching -1
     */
    public static function easyStructTest($struct)
    {
        return $struct['moe'] + $struct['larry'] + $struct['curly'];
    }
}

$options = [
    'prefix'       => 'validator1.',
    'backend'      => 'Xmlrpcext',
    'cacheOptions' => [
        'cacheDir'       => sys_get_temp_dir() . '/',
        'lifetime'       => 60,
        'cacheByDefault' => true,
    ],
    'cacheDebug' => true,
];

$server = XML_RPC2_CachedServer::create('TestServer', $options);
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<'EOS'
    <?xml version="1.0" encoding="iso-8859-1"?>
    <methodCall>
    <methodName>validator1.easyStructTest</methodName>
    <params>
     <param>
      <value>
       <struct>
        <member>
         <name>moe</name>
         <value>
          <int>5</int>
         </value>
        </member>
        <member>
         <name>larry</name>
         <value>
          <int>6</int>
         </value>
        </member>
        <member>
         <name>curly</name>
         <value>
          <int>8</int>
         </value>
        </member>
       </struct>
      </value>
     </param>
    </params>
    </methodCall>
    EOS;
$response = $server->getResponse();
$result = XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response));
var_dump($result);
$response = $server->getResponse();
$result = XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response));
var_dump($result);
$server->clean();

?>
--EXPECT--
CACHE DEBUG : default values  => weCache=true, lifetime=60
CACHE DEBUG : phpdoc comments => weCache=false, lifetime=-1
CACHE DEBUG : we don't cache !
int(19)
CACHE DEBUG : default values  => weCache=true, lifetime=60
CACHE DEBUG : phpdoc comments => weCache=false, lifetime=-1
CACHE DEBUG : we don't cache !
int(19)
