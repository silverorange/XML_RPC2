--TEST--
XMLRPCext Backend XML-RPC server Validator1 test (simpleStructReturnTest)
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
     * @param int $int a int
     *
     * @return array result
     */
    public static function simpleStructReturnTest($int)
    {
        return [
            'times10'   => 10 * $int,
            'times100'  => 100 * $int,
            'times1000' => 1000 * $int,
        ];
    }
}

$options = [
    'prefix'  => 'validator1.',
    'backend' => 'Xmlrpcext',
];

$server = XML_RPC2_Server::create('TestServer', $options);
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<'EOS'
    <?xml version="1.0" encoding="iso-8859-1"?>
    <methodCall>
    <methodName>validator1.simpleStructReturnTest</methodName>
    <params>
     <param>
      <value>
       <int>13</int>
      </value>
     </param>
    </params>
    </methodCall>
    EOS;
$response = $server->getResponse();
$result = XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response));
var_dump($result);

?>
--EXPECT--
array(3) {
  ["times10"]=>
  int(130)
  ["times100"]=>
  int(1300)
  ["times1000"]=>
  int(13000)
}
