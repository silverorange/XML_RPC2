<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (c) 2004-2006 Sergio Goncalves Carvalho                                |
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
 * This class is a server call handler which exposes a classe's static public methods.
 *
 * XML_RPC2_Server_CallHandler_Class is the preferred call handler to use when you are
 * designing your XML-RPC server from the ground up. Usage is quite simple:
 *  - Create a class holding all of the XML-RPC server's exported procedures as public static methods (the interface class).
 *  - PhpDoc the classes' methods, including at least method signature (params and return types) and short description.
 *  - Use the XML_RPC2 factory method to create a server based on the interface class.
 * A simple example:
 * <code>
 * /**
 *  * echoecho echoes the message received
 *  *
 *  * @param string  Message
 *
 *  * @return string The echo
 *  {@*}
 * class EchoServer {
 *     public static function echoecho($string)
 *     {
 *         return $string;
 *     }
 * }
 *
 * $server = XML_RPC2_Server::create('EchoServer');
 * $server->handleCall();
 * </code>
 *
 * Use this call handler if you have designed your xml-rpc external interface as a set of
 * public class methods on a given class. If, on the other hand, you intend to export an
 * already existing class, it may be that not all of the methods you want to export are static.
 * In that case, it is probably best to use XML_RPC2_Server_CallHandler_Instance instead.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 * @see       XML_RPC2_Server::create
 * @see       XML_RPC2_Server_CallHandler_Instance
 */
class XML_RPC2_Server_CallHandler_Class extends XML_RPC2_Server_CallHandler
{
    /**
     * Name of target class.
     *
     * @var string
     */
    private $_className;

    /**
     * XML_RPC2_Server_CallHandler_Class Constructor. Creates a new call handler exporting the give static class' methods.
     *
     * Before using this constructor, take a look at XML_RPC2_Server::create. The factory
     * method is usually a quicker way of instantiating the server and its call handler.
     *
     * @param string $className     The Target class. Calls will be made on this class
     * @param string $defaultPrefix Default prefix to prepend to all exported methods (defaults to '')
     *
     * @see XML_RPC2_Server::create()
     */
    public function __construct($className, $defaultPrefix)
    {
        $this->_className = $className;
        $reflection = new ReflectionClass($className);
        foreach ($reflection->getMethods() as $method) {
            if ($method->isStatic() && $method->isPublic() && !$method->isAbstract() && !$method->isConstructor()) {
                $candidate = new XML_RPC2_Server_Method($method, $defaultPrefix);
                if (!$candidate->isHidden()) {
                    $this->addMethod($candidate);
                }
            }
        }
    }

    /**
     * __call catchall. Delegate the method call to the target class, and return its result.
     *
     * @param string $methodName Name of method to call
     * @param array  $parameters Array of parameters for call
     *
     * @return mixed Whatever the target method returned
     */
    public function __call($methodName, $parameters)
    {
        if (!array_key_exists($methodName, $this->getMethods())) {
            throw new XML_RPC2_Exception_UnknownMethod("Method {$methodName} is not exported by this server");
        }

        return call_user_func_array([$this->_className, $this->getMethod($methodName)->getInternalMethod()], $parameters);
    }
}
