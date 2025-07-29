--TEST--
PHP Backend XML-RPC client against phpxmlrpc validator1 (echoStructTest)
--SKIPIF--
<?php
if (!function_exists('curl_init')) {
    echo 'Skip no CURI extension available';
}
?>
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

$options = [
    'debug'   => false,
    'backend' => 'Php',
    'prefix'  => 'validator1.',
];
$client = XML_RPC2_Client::create('https://gggeek.altervista.org/sw/xmlrpc/demo/server/server.php', $options);
$arg = [
    'moe'   => 5,
    'larry' => 6,
    'curly' => 8,
];
$result = $client->echoStructTest($arg);
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
