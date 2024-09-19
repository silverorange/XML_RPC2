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
 * XML_RPC request backend class. This class represents an XML_RPC request, exposing the methods
 * needed to encode/decode a request.
 *
 * @category  XML
 * @package   XML_RPC2
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 * @link      http://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Backend_Php_Request
{
    /**
     * Name of requested method
     *
     * @var mixed
     */
    private $_methodName = '';

    /**
     * Request parameters
     *
     * @var array
     */
    private $_parameters = null;

    /**
     * Encoding of the request
     *
     * @var string
     */
    private $_encoding = 'utf-8';

    /**
     * Create a new xml-rpc request with the provided methodname
     *
     * @param string $methodName Name of method targeted by this xml-rpc request
     * @param string $encoding   encoding of the request
     */
    public function __construct($methodName, $encoding = 'utf-8')
    {
        $this->_methodName = $methodName;
        $this->setParameters([]);
        $this->_encoding = $encoding;
    }

    /**
     * Parameters property setter
     *
     * @param mixed $value The new parameters
     *
     * @return void
     */
    public function setParameters($value)
    {
        $this->_parameters = $value;
    }

    /**
     * Parameters property appender
     *
     * @param mixed $value The new parameter
     *
     * @return void
     */
    public function addParameter($value)
    {
        $this->_parameters[] = $value;
    }

    /**
     * Parameters property getter
     *
     * @return mixed The current parameters
     */
    public function getParameters()
    {
        return $this->_parameters;
    }

    /**
     * Method name getter
     *
     * @return string method name
     */
    public function getMethodName()
    {
        return $this->_methodName;
    }

    /**
     * Encode the request for transmission.
     *
     * @return string XML-encoded request (a full XML document)
     */
    public function encode()
    {
        $methodName = $this->_methodName;
        $parameters = $this->getParameters();

        $result = '<?xml version="1.0" encoding="' . $this->_encoding . '"?' . ">\n";
        $result .= "<methodCall>";
        $result .= "<methodName>{$methodName}</methodName>";
        $result .= "<params>";
        foreach ($parameters as $parameter) {
            $result .= "<param><value>";
            $result .= ($parameter instanceof XML_RPC2_Backend_Php_Value) ? $parameter->encode() : XML_RPC2_Backend_Php_Value::createFromNative($parameter)->encode();
            $result .= "</value></param>";
        }
        $result .= "</params>";
        $result .= "</methodCall>";
        return $result;
    }

    /**
     * Decode a request from XML and construct a request object with the createFromDecoded values
     *
     * @param SimpleXMLElement $simpleXML The encoded XML-RPC request.
     *
     * @return XML_RPC2_Backend_Php_Request The xml-rpc request, represented as an object instance
     */
    public static function createFromDecode($simpleXML)
    {
        $methodName = (string) $simpleXML->methodName;
        $params = [];
        foreach ($simpleXML->params->param as $param) {
            foreach ($param->value as $value) {
                $params[] = XML_RPC2_Backend_Php_Value::createFromDecode($value)->getNativeValue();
            }
        }
        $result = new XML_RPC2_Backend_Php_Request($methodName);
        $result->setParameters($params);
        return $result;
    }
}
