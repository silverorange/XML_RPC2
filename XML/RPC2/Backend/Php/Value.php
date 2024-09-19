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
 * @copyright 2004-2005 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link      http://pear.php.net/package/XML_RPC2
 */

/**
 * XML_RPC value abstract class. All XML_RPC value classes inherit from XML_RPC2_Value
 *
 * @category  XML
 * @package   XML_RPC2
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2005 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link      http://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Backend_Php_Value extends XML_RPC2_Value
{
    /**
     * Native value
     *
     * @var mixed
     */
    private $_nativeValue = null;

    /**
     * Getter for nativeValue property
     *
     * @return mixed The current nativeValue
     */
    public function getNativeValue()
    {
        return $this->_nativeValue;
    }

    /**
     * Setter for nativeValue
     *
     * @param mixed $value the native value.
     *
     * @return void
     */
    protected function setNativeValue($value)
    {
        $this->_nativeValue = $value;
    }

    /**
     * Choose a XML_RPC2_Value subclass appropriate for the
     * given value and create it.
     *
     * This method tries to find the most adequate XML-RPC datatype to hold
     * a given PHP native type. Note that distinguishing some datatypes may be
     * difficult:
     *  - Timestamps are represented by PHP integers, so an XML_RPC2_Value_Datetime is never returned
     *  - Indexed arrays and associative arrays are the same native PHP type. In this case:
     *    a) The array's indexes start at 0 or 1 and increase monotonically with step 1, or
     *    b) they do not
     *    in the first case, an XML_RPC2_Value_Array is returned. In the second, a XML_RPC2_Value_Struct is returned.
     *  - PHP Objects are serialized and represented in an XML_RPC2_Value_Base64
     *  - Integers fitting in a 32bit integer are encoded as regular xml-rpc integers
     *  - Integers larger than 32bit are encoded using the i8 xml-rpc extension
     *
     * Whenever native object automatic detection proves inaccurate, use XML_RPC2_Value::createFromNative providing
     * a valid explicit type as second argument
     *
     * the appropriate XML_RPC2_Value child class instead.
     *
     * @param mixed  $nativeValue  The native value
     * @param string $explicitType The xml-rpc target encoding type, as per the xmlrpc spec (optional)
     *
     * @return XML_RPC2_Value the value instance.
     *
     * @throws XML_RPC2_Exception_InvalidTypeEncode When the native value has a type that can't be translated to XML_RPC
     *
     * @see XML_RPC_Client::__call
     * @see XML_RPC_Server
     */
    public static function createFromNative($nativeValue, $explicitType = null)
    {
        if (is_null($explicitType)) {
            switch (gettype($nativeValue)) {
            case 'boolean':
                $explicitType = 'boolean';
                break;
            case 'integer':
                $explicitType = 'int';
                break;
            case 'double':
                $explicitType = 'double';
                break;
            case 'string':
                $explicitType = 'string';
                break;
            case 'array':
                $explicitType = 'array';
                $keys = array_keys($nativeValue);
                if (count($keys) > 0) {
                    if ($keys[0] !== 0 && ($keys[0] !== 1)) $explicitType = 'struct';
                    $i=0;
                    do {
                        $previous = $keys[$i];
                        $i++;
                        if (array_key_exists($i, $keys) && ($keys[$i] !== ((int) $keys[$i - 1]) + 1)) $explicitType = 'struct';
                    } while (array_key_exists($i, $keys) && $explicitType == 'array');
                }
                break;
            case 'object':
                if ((mb_strtolower($nativeValue::class) === 'stdclass') && (isset($nativeValue->xmlrpc_type))) {
                    // In this case, we have a "stdclass native value" (emulate xmlrpcext)
                    // the type 'base64' or 'datetime' is given by xmlrpc_type public property
                    $explicitType = $nativeValue->xmlrpc_type;
                } else {
                    $nativeValue = serialize($nativeValue);
                    $explicitType = 'base64';
                }
                break;
            case 'resource':
            case 'NULL':
            case 'unknown type':
                throw new XML_RPC2_Exception_InvalidTypeEncode(
                    sprintf(
                        'Impossible to encode value \'%s\' from type \'%s\'. No analogous type in XML_RPC.',
                        (string) $nativeValue,
                        gettype($nativeValue)
                    )
                );
            default:
                throw new XML_RPC2_Exception_InvalidTypeEncode(
                    sprintf(
                        'Unexpected PHP native type returned by gettype: \'%s\', for value \'%s\'',
                        gettype($nativeValue),
                        (string) $nativeValue
                    )
                );
            }
        }
        $explicitType = ucfirst(mb_strtolower($explicitType));
        return match ($explicitType) {
            'I8' => XML_RPC2_Backend_Php_Value_Scalar::createFromNative($nativeValue, 'Integer64'),
            'I4', 'Int', 'Boolean', 'Double', 'String', 'Nil' => XML_RPC2_Backend_Php_Value_Scalar::createFromNative($nativeValue),
            'Datetime.iso8601', 'Datetime' => new XML_RPC2_Backend_Php_Value_Datetime($nativeValue),
            'Base64' => new XML_RPC2_Backend_Php_Value_Base64($nativeValue),
            'Array' => new XML_RPC2_Backend_Php_Value_Array($nativeValue),
            'Struct' => new XML_RPC2_Backend_Php_Value_Struct($nativeValue),
            default => throw new XML_RPC2_Exception_InvalidTypeEncode(sprintf('Unexpected explicit encoding type \'%s\'', $explicitType)),
        };
    }

    /**
     * Decode an encoded value and build the applicable XML_RPC2_Value subclass
     *
     * @param SimpleXMLElement $simpleXML The encoded XML-RPC value
     *
     * @return mixed the corresponding XML_RPC2_Value object
     */
    public static function createFromDecode($simpleXML)
    {
        // TODO Remove reparsing of XML fragment, when SimpleXML proves more solid. Currently it segfaults when
        // xpath is used both in an element and in one of its children
        $simpleXML = simplexml_load_string($simpleXML->asXML());

        $valueType = $simpleXML->xpath('./*');
        if (count($valueType) == 1) { // Usually we must check the node name
            $nodename = dom_import_simplexml($valueType[0])->nodeName;
            $nativeType = match ($nodename) {
                'i8' => 'Integer64',
                'i4', 'int' => 'Integer',
                'boolean' => 'Boolean',
                'double' => 'Double',
                'string' => 'String',
                'dateTime.iso8601' => 'Datetime',
                'base64' => 'Base64',
                'array' => 'Array',
                'struct' => 'Struct',
                'nil' => 'Nil',
                default => throw new XML_RPC2_Exception_Decode(sprintf('Unable to decode XML-RPC value. Value type is not recognized \'%s\'', $nodename)),
            };
        } elseif (count($valueType) == 0) { // Default type is string
            $nodename = null;
            $nativeType = 'String';
        } else {
            throw new XML_RPC2_Exception_Decode(sprintf('Unable to decode XML-RPC value. Value presented %s type nodes: %s.', count($valueType), $simpleXML->asXML()));
        }
        $nativeType = 'XML_RPC2_Backend_Php_Value_' . $nativeType;
        return self::createFromNative(@call_user_func([$nativeType, 'decode'], $simpleXML), $nodename);
    }

    /**
     * Encode the instance into XML, for transport
     *
     * @return string The encoded XML-RPC value,
     */
    // Declaration commented because of: http://pear.php.net/bugs/bug.php?id=8499
    // public abstract function encode();

    /**
     * Decode transport XML and set the instance value accordingly
     *
     * @param mixed The encoded XML-RPC value,
     */
    // Declaration commented because of: http://pear.php.net/bugs/bug.php?id=8499
    // public static abstract function decode($xml);
}

?>
