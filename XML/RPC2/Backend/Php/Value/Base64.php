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
 * XML_RPC base64 value class. Instances of this class represent base64-encoded string scalars in XML_RPC.
 *
 * To work on a compatible way with the xmlrpcext backend, we introduce a particular "nativeValue" which is
 * a standard class (stdclass) with two public properties :
 * scalar => the string (non encoded)
 * xmlrpc_type => 'base64'
 *
 * The constructor can be called with a "classic" string or with a such object
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Value_Base64 extends XML_RPC2_Backend_Php_Value
{
    /**
     * Constructor. Will build a new XML_RPC2_Backend_Php_Value_Base64 with the given value.
     *
     * This class handles encoding-decoding internally. Do not provide the
     * native string base64-encoded
     *
     * @param mixed $nativeValue to be transmited base64-encoded or "stdclass native value"
     */
    public function __construct($nativeValue)
    {
        if (is_object($nativeValue) && (mb_strtolower($nativeValue::class) === 'stdclass') && (isset($nativeValue->xmlrpc_type))) {
            $scalar = $nativeValue->scalar;
        } else {
            if (!is_string($nativeValue)) {
                throw new XML_RPC2_Exception_InvalidType(sprintf('Cannot create XML_RPC2_Backend_Php_Value_Base64 from type \'%s\'.', gettype($nativeValue)));
            }
            $scalar = $nativeValue;
        }
        $tmp = new stdClass();
        $tmp->scalar = $scalar;
        $tmp->xmlrpc_type = 'base64';
        $this->setNativeValue($tmp);
    }

    /**
     * Encode the instance into XML, for transport.
     *
     * @return string The encoded XML-RPC value,
     */
    public function encode()
    {
        $native = $this->getNativeValue();

        return '<base64>' . base64_encode($native->scalar) . '</base64>';
    }

    /**
     * Decode transport XML and set the instance value accordingly.
     *
     * @param mixed $xml The encoded XML-RPC value,
     *
     * @return stdClass decoded object with keys scalar and xml_rpctype
     */
    public static function decode($xml)
    {
        // TODO Remove reparsing of XML fragment, when SimpleXML proves more solid. Currently it segfaults when
        // xpath is used both in an element and in one of its children
        $xml = simplexml_load_string($xml->asXML());
        $value = $xml->xpath('/value/base64/text()');
        if (!array_key_exists(0, $value)) {
            $value = $xml->xpath('/value/text()');
        }
        // Emulate xmlrpcext results (to be able to switch from a backend to another)
        $result = new stdClass();
        $result->scalar = base64_decode($value[0]);
        $result->xmlrpc_type = 'base64';

        return $result;
    }
}
