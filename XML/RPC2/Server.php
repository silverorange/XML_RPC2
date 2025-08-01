<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (c) 2004-2006 Sergio Gonalves Carvalho                            |
 * +-----------------------------------------------------------------------------+
 * | This file is part of XML_RPC2.                                              |
 * |                                                                             |
 * | XML_RPC2 is free software; you can redistribute it and/or modify            |
 * | it under the terms of the GNU Lesser General Public License as published by |
 * | the Free Software Foundation; either version 2.1 of the License, or         |
 * | (at your option) any later version.                                         |
 * |                                                                             |
 * | XML_RPC2 is distributed in the hope that it will be useful,                 |
 * | but WITHOUT ANY WARRANTY; without even the implied warranty of              |
 * | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the               |
 * | GNU Lesser General Public License for more details.                         |
 * |                                                                             |
 * | You should have received a copy of the GNU Lesser General Public License    |
 * | along with XML_RPC2; if not, write to the Free Software                     |
 * | Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA                    |
 * | 02111-1307 USA                                                              |
 * +-----------------------------------------------------------------------------+
 * | Author: Sergio Carvalho <sergio.carvalho@portugalmail.com>                  |
 * +-----------------------------------------------------------------------------+.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see      http://pear.php.net/package/XML_RPC2
 */

/**
 * XML_RPC2_Server is the frontend class for exposing PHP functions via XML-RPC.
 *
 * Exporting a programatic interface via XML-RPC using XML_RPC2 is exceedingly easy:
 *
 * The first step is to assemble all methods you wish to export into a class. You may either
 * create a (abstract) class with exportable methods as static, or use an existing instance
 * of an object.
 *
 * You'll then need to document the methods using PHPDocumentor tags. XML_RPC2 will use the
 * documentation for server introspection. You'll get something like this:
 *
 * <code>
 * class ExampleServer {
 *     /**
 *      * hello says hello
 *      *
 *      * @param string  Name
 *
 *      * @return string Greetings
 *      {@*}
 *     public static function hello($name)
 * {
 *         return "Hello $name";
 *     }
 * }
 * </code>
 *
 * Now, instantiate the server, using the Factory method to select a backend and a call handler for you:
 * <code>
 * $server = XML_RPC2_Server::create('ExampleServer');
 * $server->handleCall();
 * </code>
 *
 * This will create a server exporting all of the 'ExampleServer' class' methods. If you wish to export
 * instance methods as well, pass an object instance to the factory instead:
 * <code>
 * $server = XML_RPC2_Server::create(new ExampleServer());
 * $server->handleCall();
 * </code>
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Server
{
    /**
     * The call handler responsible for executing the server exported methods.
     *
     * @var mixed
     */
    protected $callHandler;

    /**
     * Prefix field.
     *
     * @var string
     */
    protected $prefix = '';

    /**
     * Encoding field.
     *
     * TODO: work on encoding for this backend
     *
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * Display html documentation of XML-RPC exported methods when there is no
     * post data.
     *
     * @var bool
     */
    protected $autoDocument = true;

    /**
     * Display external links at the end of autodocumented page.
     *
     * @var bool
     */
    protected $autoDocumentExternalLinks = true;

    /**
     * Signature checking flag.
     *
     * If set to true, the server will check the method signature before
     * calling the corresponding php method.
     *
     * @var bool
     */
    protected $signatureChecking = true;

    /**
     * Input handler.
     *
     * Implementation of XML_RPC2_Server_Input that feeds this server with
     * input
     *
     * @var XML_RPC2_Server_Input
     */
    protected $input;

    /**
     * Creates a new XML-RPC Server.
     *
     * @param object $callHandler the call handler will receive a method call
     *                            for each remote call received
     * @param array  $options     associative array of options
     */
    protected function __construct($callHandler, $options = [])
    {
        $this->callHandler = $callHandler;
        if ((isset($options['prefix'])) && is_string($options['prefix'])) {
            $this->prefix = $options['prefix'];
        }
        if ((isset($options['encoding'])) && is_string($options['encoding'])) {
            $this->encoding = $options['encoding'];
        }
        if ((isset($options['autoDocument'])) && is_bool($options['autoDocument'])) {
            $this->autoDocument = $options['autoDocument'];
        }
        if ((isset($options['autoDocumentExternalLinks'])) && is_bool($options['autoDocumentExternalLinks'])) {
            $this->autoDocumentExternalLinks = $options['autoDocumentExternalLinks'];
        }
        if ((isset($options['signatureChecking'])) && is_bool($options['signatureChecking'])) {
            $this->signatureChecking = $options['signatureChecking'];
        }
        if (!isset($options['input'])) {
            $options['input'] = 'XML_RPC2_Server_Input_RawPostData';
        }
        if (is_string($options['input'])) {
            $inputClass = $options['input'];
            $options['input'] = new $inputClass();
        }
        if ($options['input'] instanceof XML_RPC2_Server_Input) {
            $this->input = $options['input'];
        } else {
            throw new XML_RPC2_Exception_Config('Invalid value for "input" option. It must be either a XML_RPC2_Server_Input subclass name or XML_RPC2_Server_Input subclass instance');
        }
    }

    /**
     * Factory method to select a backend and return a new XML_RPC2_Server
     * based on the backend.
     *
     * @param mixed $callTarget either a class name or an object instance
     * @param array $options    associative array of options
     *
     * @return object a server class instance
     */
    public static function create($callTarget, $options = [])
    {
        if (isset($options['backend'])) {
            XML_RPC2_Backend::setBackend($options['backend']);
        }
        if (isset($options['prefix'])) {
            $prefix = $options['prefix'];
        } else {
            $prefix = '';
        }
        $backend = XML_RPC2_Backend::getServerClassname();
        // Find callHandler class
        if (!isset($options['callHandler'])) {
            if (is_object($callTarget)) {
                // Delegate calls to instance methods
                $callHandler = new XML_RPC2_Server_CallHandler_Instance($callTarget, $prefix);
            } else {
                // Delegate calls to static class methods
                $callHandler = new XML_RPC2_Server_CallHandler_Class($callTarget, $prefix);
            }
        } else {
            $callHandler = $options['callHandler'];
        }

        return new $backend($callHandler, $options);
    }

    /**
     * Receive the XML-RPC request, decode the HTTP payload, delegate
     * execution to the call handler, and output the encoded call handler
     * response.
     *
     * @note The encoded call handler response is echoed, not returned.
     */
    abstract public function handleCall();

    /**
     * Transforms an error into an exception.
     *
     * @param int    $errno   error number
     * @param string $errstr  error string
     * @param string $errfile error file
     * @param int    $errline error line
     *
     * @throws Exception the exception
     */
    public static function errorToException($errno, $errstr, $errfile, $errline)
    {
        switch ($errno) {
            case E_WARNING:
            case E_NOTICE:
            case E_USER_WARNING:
            case E_USER_NOTICE:
            case E_STRICT:
                // Silence warnings
                // TODO Logging should occur here
                break;

            default:
                throw new Exception(
                    'Classic error reported "' . $errstr . '" on '
                    . $errfile . ':' . $errline
                );
        }
    }

    /**
     * Print an HTML page from the result of server introspection.
     */
    public function autoDocument(): void
    {
        echo "<!DOCTYPE html PUBLIC \"-//W3C//DTD XHTML 1.0 Strict//EN\" \"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd\">\n";
        echo "<html xmlns=\"http://www.w3.org/1999/xhtml\" lang=\"en\" xml:lang=\"en\">\n";
        echo "  <head>\n";
        echo '    <meta http-equiv="Content-Type" content="text/HTML; charset=' . $this->encoding . "\"  />\n";
        echo "    <title>Available XMLRPC methods for this server</title>\n";
        echo "    <style type=\"text/css\">\n";
        echo "      li,p { font-size: 10pt; font-family: Arial,Helvetia,sans-serif; }\n";
        echo "      a:link { background-color: white; color: blue; text-decoration: underline; font-weight: bold; }\n";
        echo "      a:visited { background-color: white; color: blue; text-decoration: underline; font-weight: bold; }\n";
        echo "      table { border-collapse:collapse; width: 100% }\n";
        echo "      table,td { padding: 5px; border: 1px solid black; }\n";
        echo "      div.bloc { border: 1px dashed gray; padding: 10px; margin-bottom: 20px; }\n";
        echo "      div.description { border: 1px solid black; padding: 10px; }\n";
        echo "      span.type { background-color: white; color: gray; font-weight: normal; }\n";
        echo "      span.paratype { background-color: white; color: gray; font-weight: normal; }\n";
        echo "      span.name { background-color: white; color: #660000; }\n";
        echo "      span.paraname { background-color: white; color: #336600; }\n";
        echo "      img { border: 0px; }\n";
        echo "      li { font-size: 12pt; }\n";
        echo "    </style>\n";
        echo "  </head>\n";
        echo "  <body>\n";
        echo "    <h1>Available XMLRPC methods for this server</h1>\n";
        echo "    <h2><a name=\"index\">Index</a></h2>\n";
        echo "    <ul>\n";
        foreach ($this->callHandler->getMethods() as $method) {
            $name = $method->getName();
            $id = md5($name);
            $signature = $method->getHTMLSignature();
            echo "      <li><a href=\"#{$id}\">{$name}()</a></li>\n";
        }
        echo "    </ul>\n";
        echo "    <h2>Details</h2>\n";
        foreach ($this->callHandler->getMethods() as $method) {
            echo "    <div class=\"bloc\">\n";
            $method->autoDocument();
            echo "      <p>(return to <a href=\"#index\">index</a>)</p>\n";
            echo "    </div>\n";
        }
        if (!$this->autoDocumentExternalLinks) {
            echo '    <p><a href="http://pear.php.net/packages/XML_RPC2"><img src="http://pear.php.net/gifs/pear-power.png" alt="Powered by PEAR/XML_RPC2" height="31" width="88" /></a> &nbsp; &nbsp; &nbsp; <a href="http://validator.w3.org/check?uri=referer"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Strict" height="31" width="88" /></a> &nbsp; &nbsp; &nbsp; <a href="http://jigsaw.w3.org/css-validator/"><img style="border:0;width:88px;height:31px" src="http://jigsaw.w3.org/css-validator/images/vcss" alt="Valid CSS!" /></a></p>' . "\n";
        }
        echo "  </body>\n";
        echo "</html>\n";
    }

    /**
     * Gets the content legth of a serialized XML-RPC message in bytes.
     *
     * @param string $content the serialized XML-RPC message
     *
     * @return int the content length in bytes
     */
    protected function getContentLength($content)
    {
        return mb_strlen($content, '8bit');
    }
}
