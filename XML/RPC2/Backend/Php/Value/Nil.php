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
 * XML_RPC null value class. Instances of this class represent null scalars in XML_RPC
 *
 * @category  XML
 * @package   XML_RPC2
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link      http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Value_Nil extends XML_RPC2_Backend_Php_Value_Scalar
{
    /**
     * Will build a new XML_RPC2_Backend_Php_Value_Nil with the given value
     */
    public function __construct()
    {
        $this->setScalarType('nil');
        $this->setNativeValue(null);
    }

    // }}}

    /**
     * Decode transport XML and set the instance value accordingly
     *
     * @param mixed $xml The XML-RPC value to decode.
     *
     * @return the decoded value.
     */
    public static function decode($xml)
    {
        return null;
    }
}

?>
