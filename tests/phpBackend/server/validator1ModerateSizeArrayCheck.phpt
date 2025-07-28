--TEST--
PHP Backend XML-RPC server Validator1 test (moderateSizeArrayCheck)
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
     * @param array $array an array
     *
     * @return string result
     */
    public static function moderateSizeArrayCheck($array)
    {
        return $array[0] . $array[count($array) - 1];
    }
}

$options = [
    'prefix'  => 'validator1.',
    'backend' => 'Php',
];

$server = XML_RPC2_Server::create('TestServer', $options);
$GLOBALS['HTTP_RAW_POST_DATA'] = <<<'EOS'
    <?xml version="1.0" encoding="iso-8859-1"?>
    <methodCall>
    <methodName>validator1.moderateSizeArrayCheck</methodName>
    <params>
     <param>
      <value>
       <array>
        <data>
         <value>
          <string>foo</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bla bla bla</string>
         </value>
         <value>
          <string>bar</string>
         </value>
        </data>
       </array>
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
string(6) "foobar"
