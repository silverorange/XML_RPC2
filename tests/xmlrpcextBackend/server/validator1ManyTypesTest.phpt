--TEST--
XMLRPCext Backend XML-RPC server Validator1 test (manyTypesTest)
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
     * @param int    $int      an integer
     * @param bool   $bool     a boolean
     * @param string $string   a string
     * @param float  $double   a float/double
     * @param mixed  $datetime a datetime
     * @param mixed  $base64   a base64 encoded string
     *
     * @return array result
     */
    public static function manyTypesTest($int, $bool, $string, $double, $datetime, $base64)
    {
        return [$int, $bool, $string, $double, $datetime, $base64];
    }
}

date_default_timezone_set('UTC');

$options = [
    'prefix'  => 'validator1.',
    'backend' => 'Xmlrpcext',
];

$server = XML_RPC2_Server::create('TestServer', $options);
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<'EOS'
    <?xml version="1.0" encoding="iso-8859-1"?>
    <methodCall>
    <methodName>validator1.manyTypesTest</methodName>
    <params>
     <param>
      <value>
       <int>1</int>
      </value>
     </param>
     <param>
      <value>
       <boolean>1</boolean>
      </value>
     </param>
     <param>
      <value>
       <string>foo</string>
      </value>
     </param>
     <param>
      <value>
       <double>3.141590</double>
      </value>
     </param>
     <param>
      <value>
       <dateTime.iso8601>20060116T19:14:03</dateTime.iso8601>
      </value>
     </param>
     <param>
      <value>
       <base64>Zm9vYmFy&#10;</base64>
      </value>
     </param>
    </params>
    </methodCall>
    EOS;
$response = $server->getResponse();
$result = XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response));
var_dump($result[0]);
var_dump($result[1]);
var_dump($result[2]);
var_dump($result[3]);
var_dump($result[4]->xmlrpc_type);
var_dump($result[4]->scalar);
var_dump($result[4]->timestamp);
var_dump($result[5]->xmlrpc_type);
var_dump($result[5]->scalar);

?>
--EXPECT--
int(1)
bool(true)
string(3) "foo"
float(3.14159)
string(8) "datetime"
string(17) "20060116T19:14:03"
int(1137438843)
string(6) "base64"
string(6) "foobar"
