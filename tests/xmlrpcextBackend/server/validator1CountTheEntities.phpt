--TEST--
XMLRPCext Backend XML-RPC server Validator1 test (countTheEntities)
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
     * @param string a string
     * @param mixed $string
     *
     * @return array result
     */
    public static function countTheEntities($string)
    {
        $ctLeftAngleBrackets = mb_substr_count($string, '<');
        $ctRightAngleBrackets = mb_substr_count($string, '>');
        $ctAmpersands = mb_substr_count($string, '&');
        $ctApostrophes = mb_substr_count($string, "'");
        $ctQuotes = mb_substr_count($string, '"');

        return [
            'ctLeftAngleBrackets'  => $ctLeftAngleBrackets,
            'ctRightAngleBrackets' => $ctRightAngleBrackets,
            'ctAmpersands'         => $ctAmpersands,
            'ctApostrophes'        => $ctApostrophes,
            'ctQuotes'             => $ctQuotes,
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
    <methodName>validator1.countTheEntities</methodName>
    <params>
     <param>
      <value>
       <string>foo &#60;&#60;&#60; bar '&#62; &#38;&#38; '' #fo&#62;o &#34; bar</string>
      </value>
     </param>
    </params>
    </methodCall>
    EOS;
$response = $server->getResponse();
$result = XML_RPC2_Backend_Php_Response::decode(simplexml_load_string($response));
var_dump($result['ctLeftAngleBrackets']);
var_dump($result['ctRightAngleBrackets']);
var_dump($result['ctAmpersands']);
var_dump($result['ctApostrophes']);
var_dump($result['ctQuotes']);

?>
--EXPECT--
int(3)
int(2)
int(2)
int(3)
int(1)
