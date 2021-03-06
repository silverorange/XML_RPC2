XML_RPC2
========
XML_RPC2 is a package providing XML-RPC client and server services. XML-RPC is
a simple remote procedure call protocol built using HTTP as transport and XML
as the encoding.As a client library, XML_RPC2 is capable of creating a proxy
class which exposes the methods exported by the server. As a server library,
XML_RPC2 is capable of exposing methods from a class or object instance,
seamlessly exporting local methods as remotely callable procedures.

This fork maintained by silverorange updates the package for PHP 7 and
composer compatibility.


Basic Usage
-----------
```php
<?php

$options = array(
  'prefix' => 'package.'
);

$client = XML_RPC2_Client::create(
  'http://pear.php.net/xmlrpc.php',
  $options
);

try {
  $result = $client->info('XML_RPC2');
  print_r($result);
} catch (XML_RPC2_Exception_Fault $e) {
  // The XMLRPC server returns a XMLRPC error
  die('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString());
} catch (Exception $e) {
  // Other errors (HTTP or networking problems...)
  die('Exception : ' . $e->getMessage());
}

?>
```

Installation
------------
Make sure the silverorange composer repository is added to the `composer.json`
for the project and then run:

```
composer require silverorange/xml_rpc2
```
