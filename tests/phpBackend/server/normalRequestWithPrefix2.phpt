--TEST--
PHP Backend XML-RPC server with normal response (with prefix in phpdoc)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

class EchoServer
{
    /**
     * echoecho echoes the message received.
     *
     * @param string  Message
     * @param mixed $string
     *
     * @xmlrpc.prefix prefixed.
     *
     * @return string The echo
     */
    public static function echoecho($string)
    {
        return $string;
    }
}

XML_RPC2_Backend::setBackend('php');
$server = XML_RPC2_Server::create('EchoServer');
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<'EOS'
    <?xml version="1.0"?>
    <methodCall>
     <methodName>prefixed.echoecho</methodName>
     <params><param><value><string>World</string></value></param></params>
    </methodCall>
    EOS;
$response = $server->getResponse();
var_dump(XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response)));
?>
--EXPECT--
string(5) "World"
