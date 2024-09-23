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
 * XML_RPC2 client helper class.
 *
 * XML_RPC2_Client must maintain a function namespace as clean as possible. As such
 * whenever possible, methods that may be usefull to subclasses but shouldn't be defined
 * in XML_RPC2 because of namespace pollution are defined here.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_ClientHelper
{
    /**
     * Display debug informations.
     *
     * @param string $request XML client request
     * @param string $body    XML server response
     */
    public static function printPreParseDebugInfo($request, $body)
    {
        echo '<pre>';
        echo "***** Request *****\n";
        echo htmlspecialchars($request);
        echo "***** End Of request *****\n\n";
        echo "***** Server response *****\n";
        echo htmlspecialchars($body);
        echo "\n***** End of server response *****\n\n";
    }

    /**
     * Display debug informations (part 2).
     *
     * @param mixed $result decoded server response
     */
    public static function printPostRequestDebugInformation($result)
    {
        echo "***** Decoded result *****\n";
        print_r($result);
        echo "\n***** End of decoded result *****";
        echo '</pre>';
    }

    /**
     * Return true is the given method name is ok with XML/RPC spec.
     *
     * NB : The '___' at the end of the method name is to avoid collisions with
     * XMLRPC __call()
     *
     * @param string $methodName the method name
     *
     * @return bool true if ok
     */
    public static function testMethodName($methodName)
    {
        return preg_match('~^[a-zA-Z0-9_.:/]*$~', $methodName);
    }
}
