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
 * XML_RPC2_Exception_Fault signals a XML-RPC response that contains a fault element instead of a regular params element.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Exception_Fault extends XML_RPC2_Exception
{
    /**
     * Fault code (in the response body).
     *
     * @var string
     */
    protected $faultCode;

    /**
     * Construct a new XML_RPC2_Exception_Fault with a given message string and
     * fault code.
     *
     * @param string $messageString the message string, corresponding to the
     *                              faultString present in the response body
     * @param string $faultCode     the fault code, corresponding to the
     *                              faultCode in the response body
     */
    public function __construct($messageString, $faultCode)
    {
        parent::__construct($messageString);
        $this->faultCode = $faultCode;
    }

    /**
     * FaultCode getter.
     *
     * @return string fault code
     */
    public function getFaultCode()
    {
        return $this->faultCode;
    }

    /**
     * FaultString getter.
     *
     * This is an alias to getMessage() in order to respect XML-RPC
     * nomenclature for faults.
     *
     * @return string fault code
     */
    public function getFaultString()
    {
        return $this->getMessage();
    }

    /**
     * Create a XML_RPC2_Exception_Fault by decoding the corresponding XML
     * string.
     *
     * @param string $xml the raw XML string
     *
     * @return XML_RPC2_Exception_Fault the parsed fault exception
     */
    public static function createFromDecode($xml)
    {
        // This is the only way I know of creating a new Document rooted in the provided simpleXMLFragment (needed for the xpath expressions that does not segfault sometimes
        $xml = simplexml_load_string($xml->asXML());
        $struct = XML_RPC2_Backend_Php_Value::createFromDecode($xml->value)->getNativeValue();
        if (!(is_array($struct)
            && array_key_exists('faultString', $struct)
            && array_key_exists('faultCode', $struct))
        ) {
            throw new XML_RPC2_Exception_Decode(
                'Unable to decode XML-RPC fault payload'
            );
        }

        return new XML_RPC2_Exception_Fault(
            $struct['faultString'],
            $struct['faultCode']
        );
    }
}
