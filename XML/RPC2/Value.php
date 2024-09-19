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
 * +-----------------------------------------------------------------------------+
 *
 * @category  XML
 * @package   XML_RPC2
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link      http://pear.php.net/package/XML_RPC2
 */

/**
 * XML_RPC value abstract class
 *
 * All XML_RPC value classes inherit from XML_RPC2_Value
 *
 * @category  XML
 * @package   XML_RPC2
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link      http://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Value
{
    /**
     * Factory method that constructs the appropriate XML-RPC encoded type
     * value
     *
     * @param mixed  $value        The value to be encode.
     * @param string $explicitType optional. The explicit XML-RPC type as
     *                             enumerated in the XML-RPC spec (defaults to
     *                             automatically selected type)
     *
     * @return mixed the encoded value.
     */
    public static function createFromNative($value, $explicitType = null)
    {
        $xmlrpcTypes = ['int', 'boolean', 'string', 'double', 'datetime', 'base64', 'struct', 'array'];
        if (in_array($explicitType, $xmlrpcTypes)) {
            return @call_user_func([XML_RPC2_Backend::getValueClassname(), 'createFromNative'], $value, $explicitType);
        }
        return $value;
    }
}

?>
