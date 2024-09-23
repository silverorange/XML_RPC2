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
 * XML_RPC struct value class. Represents values of type struct (associative struct).
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Value_Struct extends XML_RPC2_Backend_Php_Value
{
    /**
     * Constructor. Will build a new XML_RPC2_Backend_Php_Value_Scalar with the given nativeValue.
     *
     * @param mixed $nativeValue the native value
     */
    public function __construct($nativeValue)
    {
        $this->setNativeValue($nativeValue);
    }

    /**
     * NativeValue property setter.
     *
     * @param mixed $value value the new nativeValue
     */
    protected function setNativeValue($value)
    {
        if (!is_array($value)) {
            throw new XML_RPC2_Exception_InvalidType(
                sprintf(
                    'Cannot create XML_RPC2_Backend_Php_Value_Struct from ' .
                    'type \'%s\'.',
                    gettype($value)
                )
            );
        }
        parent::setNativeValue($value);
    }

    /**
     * Encode the instance into XML, for transport.
     *
     * @return string The encoded XML-RPC value,
     */
    public function encode()
    {
        $result = '<struct>';
        foreach ($this->getNativeValue() as $name => $element) {
            $result .= '<member>';
            $result .= '<name>';
            $result .= strtr($name, ['&' => '&amp;', '<' => '&lt;', '>' => '&gt;']);
            $result .= '</name>';
            $result .= '<value>';
            $result .= ($element instanceof XML_RPC2_Backend_Php_Value)
                ? $element->encode()
                : XML_RPC2_Backend_Php_Value::createFromNative($element)->encode();

            $result .= '</value>';
            $result .= '</member>';
        }
        $result .= '</struct>';

        return $result;
    }

    /**
     * Decode transport XML and set the instance value accordingly.
     *
     * @param mixed $xml the encoded XML-RPC value
     *
     * @return array the parsed struct
     */
    public static function decode($xml)
    {
        // TODO Remove reparsing of XML fragment, when SimpleXML proves more solid. Currently it segfaults when
        // xpath is used both in an element and in one of its children
        $xml = simplexml_load_string($xml->asXML());
        $values = $xml->xpath('/value/struct/member');
        $result = [];
        foreach (array_keys($values) as $i) {
            $result[(string) $values[$i]->name] = XML_RPC2_Backend_Php_Value::createFromDecode($values[$i]->value)->getNativeValue();
        }

        return $result;
    }
}
