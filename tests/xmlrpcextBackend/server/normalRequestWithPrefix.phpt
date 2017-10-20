--TEST--
XMLRPCext Backend XML-RPC server with normal response (with prefix)
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    print "Skip XMLRPC extension unavailable";
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

class EchoServer {
    /**
     * echoecho echoes the message received
     *
     * @param string  Message
     * @return string The echo
     */
    public static function echoecho($string) {
        return $string;
    }
}

XML_RPC2_Backend::setBackend('XMLRPCext');
$server = XML_RPC2_Server::create('EchoServer', array('prefix' => 'prefixed.'));
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<EOS
<?xml version="1.0"?>
<methodCall>
 <methodName>prefixed.echoecho</methodName>
 <params><param><value><string>World</string></value></param></params>
</methodCall>
EOS
;
$response = $server->getResponse();
var_dump(XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response)));
?>
--EXPECT--
string(5) "World"
