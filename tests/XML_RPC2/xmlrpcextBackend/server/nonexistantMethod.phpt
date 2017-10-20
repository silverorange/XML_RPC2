--TEST--
XMLRPCext Backend XML-RPC server with normal response
--SKIPIF--
<?php
if (!function_exists('xmlrpc_server_create')) {
    print "Skip XMLRPC extension unavailable";
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../../vendor/autoload.php';

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
$server = XML_RPC2_Server::create('EchoServer');
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<EOS
<?xml version="1.0"?>
<methodCall>
 <methodName>moo</methodName>
 <params><param><value><string>World</string></value></param></params>
</methodCall>
EOS
;
$response = $server->getResponse();
try {
    XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response));
} catch (XML_RPC2_Exception_Fault $e) {
    var_dump($e->getFaultCode());
    var_dump($e->getMessage());
}

?>
--EXPECT--
int(-32601)
string(40) "server error. requested method not found"
