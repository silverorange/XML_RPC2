--TEST--
PHP Backend XML-RPC server Validator1 test (echoStructTest)
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

class TestServer {
    /**
     * test function
     *
     * see http://www.xmlrpc.com/validator1Docs
     *
     * @param array $struct a struct
     * @return array result
     */
    public static function echoStructTest($struct) {
        return $struct;
    }
}

$options = array(
    'prefix' => 'validator1.',
    'backend' => 'Php'
);

$server = XML_RPC2_Server::create('TestServer', $options);
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<EOS
<?xml version="1.0" encoding="iso-8859-1"?>
<methodCall>
<methodName>validator1.echoStructTest</methodName>
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
EOS
;
$response = $server->getResponse();
$result = (XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response)));
var_dump($result);

?>
--EXPECT--
array(3) {
  ["moe"]=>
  int(5)
  ["larry"]=>
  int(6)
  ["curly"]=>
  int(8)
}
