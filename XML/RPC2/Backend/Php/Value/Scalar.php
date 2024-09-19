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
 * XML_RPC scalar value abstract class. All XML_RPC value classes representing scalar types inherit from XML_RPC2_Value_Scalar.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see      http://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Backend_Php_Value_Scalar extends XML_RPC2_Backend_Php_Value
{
    /**
     * Scalar type.
     *
     * @var string
     */
    private $_scalarType;

    /**
     * Constructor. Will build a new XML_RPC2_Value_Scalar with the given nativeValue.
     *
     * @param string $scalarType  the scalar type to represent
     * @param mixed  $nativeValue the native value
     */
    public function __construct($scalarType, $nativeValue)
    {
        $this->setScalarType($scalarType);
        $this->setNativeValue($nativeValue);
    }

    /**
     * Property setter for scalarType.
     *
     * @param mixed $value The new scalarType
     */
    protected function setScalarType($value)
    {
        $this->_scalarType = match ($value) {
            'nil', 'int', 'i8', 'i4', 'boolean', 'string', 'double', 'dateTime.iso8601', 'base64' => $value,
            default => throw new XML_RPC2_Exception_InvalidType(sprintf('Type \'%s\' is not an XML-RPC scalar type', $value)),
        };
    }

    /**
     * Property getter for scalarType.
     *
     * @return mixed The current scalarType
     */
    public function getScalarType()
    {
        return $this->_scalarType;
    }

    /**
     * Choose a XML_RPC2_Value subclass appropriate for the
     * given value and create it.
     *
     * @param string $nativeValue  The native value
     * @param string $explicitType Optionally, the scalar type to use
     *
     * @return XML_RPC2_Value The newly created value
     *
     * @throws XML_RPC2_Exception_InvalidTypeEncode When native value's type is not a native type
     */
    public static function createFromNative($nativeValue, $explicitType = null)
    {
        if (is_null($explicitType)) {
            $explicitType = match (gettype($nativeValue)) {
                'integer' => $nativeValue <= 2147483647 /* PHP_INT_MAX on 32 bit systems */ ? gettype($nativeValue) : 'Integer64',
                'NULL'    => 'Nil',
                'boolean', 'double', 'string' => gettype($nativeValue),
                default => throw new XML_RPC2_Exception_InvalidTypeEncode(
                    sprintf(
                        'Impossible to encode scalar value \'%s\' from type \'%s\'. Native type is not a scalar XML_RPC type (boolean, integer, double, string)',
                        (string) $nativeValue,
                        gettype($nativeValue)
                    )
                ),
            };
        }
        $explicitType = ucfirst(mb_strtolower($explicitType));
        $explicitType = sprintf('XML_RPC2_Backend_Php_Value_%s', $explicitType);

        return new $explicitType($nativeValue);
    }

    /**
     * Encode the instance into XML, for transport.
     *
     * @return string The encoded XML-RPC value,
     */
    public function encode()
    {
        return '<' . $this->getScalarType() . '>' . $this->getNativeValue() . '</' . $this->getScalarType() . '>';
    }
}
