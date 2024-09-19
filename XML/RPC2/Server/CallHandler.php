<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (c) 2004-2006 Sergio Gonalves Carvalho                                |
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
 * A CallHandler is responsible for actually calling the server-exported methods from the exported class.
 *
 * This class is abstract and not meant to be used directly by XML_RPC2 users.
 *
 * XML_RPC2_Server_CallHandler provides the basic code for a call handler class. An XML_RPC2 Call Handler
 * operates in tandem with an XML_RPC2 server to export a classe's methods. While XML_RPC2 Server
 * is responsible for request decoding and response encoding, the Call Handler is responsible for
 * delegating the actual method call to the intended target.
 *
 * Different server behaviours can be obtained by plugging different Call Handlers into the XML_RPC2_Server.
 * Namely, there are two call handlers available:
 *  - XML_RPC2_Server_Callhandler_Class: Which exports a classe's public static methods
 *  - XML_RPC2_Server_Callhandler_Instance: Which exports an object's pubilc methods
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see      http://pear.php.net/package/XML_RPC2
 * @see       XML_RPC2_Server_Callhandler_Class
 * @see       XML_RPC2_Server_Callhandler_Instance
 */
abstract class XML_RPC2_Server_CallHandler
{
    /**
     * Holds server methods.
     *
     * @var array
     */
    protected $methods = [];

    /**
     * Methods getter.
     *
     * @return array Array of XML_RPC2_Server_Method instances
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Method appender.
     *
     * @param XML_RPC2_Server_Method $method Method to append to methods
     */
    protected function addMethod(XML_RPC2_Server_Method $method)
    {
        $this->methods[$method->getName()] = $method;
    }

    /**
     * Method getter.
     *
     * @param string $name Name of method to return
     *
     * @return XML_RPC2_Server_Method the named method
     */
    public function getMethod($name)
    {
        return $this->methods[$name] ?? false;
    }
}
