--TEST--
Regression guard against bug 11135: Empty array should not trigger notice
--FILE--
<?php

require_once __DIR__ . '/../../../vendor/autoload.php';

/**
 * @internal
 *
 * @coversNothing
 */
class Empty_Array_Value_Test extends XML_RPC2_Backend_Php_Value {}

function errorHandler(
    $errno,
    $errstr,
    $errfile,
    $errline,
    $errcontext
) {
    echo $errno;
}

//    set_error_handler('errorHandler');

$array_class = new Empty_Array_Value_Test();
$array_class->createFromNative([]);

?>
--EXPECTREGEX--
^$
