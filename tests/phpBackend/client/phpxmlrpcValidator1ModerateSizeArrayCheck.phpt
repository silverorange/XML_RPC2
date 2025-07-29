--TEST--
PHP Backend XML-RPC client against phpxmlrpc validator1 (moderateSizeArrayCheck)
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
$tmp = ['foo'];
for ($i = 0; $i < 150; $i++) {
    $tmp[] = 'bla bla bla';
}
$tmp[] = 'bar';
$result = $client->moderateSizeArrayCheck($tmp);
echo $result . "\n";

?>
--EXPECT--
foobar
