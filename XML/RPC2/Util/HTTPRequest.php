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
 * XML_RPC utility HTTP request class. This class mimics a subset of PEAR's HTTP_Request
 * and is to be refactored out of the package once HTTP_Request releases an E_STRICT version.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2011 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Util_HTTPRequest
{
    /**
     * Proxy field.
     *
     * @var string
     */
    private $_proxy;

    /**
     * Proxyauth field.
     *
     * @var string
     */
    private $_proxyAuth;

    /**
     * PostData field.
     *
     * @var string
     */
    private $_postData;

    /**
     * URI field.
     *
     * @var array
     */
    private $_uri;

    /**
     * Encoding for the request.
     *
     * @var string
     */
    private $_encoding = 'utf-8';

    /**
     * SSL verify flag.
     *
     * @var bool
     */
    private $_sslverify = true;

    /**
     * HTTP timeout length in seconds.
     *
     * @var int
     */
    private $_connectionTimeout;

    /**
     * HTTP_Request2 backend.
     *
     * @var int
     */
    private $_httpRequest;

    private string $_body = '';

    /**
     * Constructor.
     *
     * Sets up the object
     *
     * @param string $uri    The uri to fetch/access
     * @param array  $params Associative array of parameters which can have
     *                       the following keys:
     *                       - proxy             - Proxy (string)
     *                       - encoding          - The request encoding (string)
     *                       - sslverify         - The SSL verify flag (boolean)
     *                       - connectionTimeout - The connection timeout in milliseconds (integer)
     *                       - httpRequest       - Preconfigured instance of HTTP_Request2 (optional)
     */
    public function __construct($uri = '', $params = [])
    {
        if (!preg_match('/(https?:\/\/)(.*)/', $uri)) {
            throw new XML_RPC2_Exception('Unable to parse URI');
        }
        $this->_uri = $uri;
        if (isset($params['encoding'])) {
            $this->_encoding = $params['encoding'];
        }
        if (isset($params['proxy'])) {
            $proxy = $params['proxy'];
            $elements = parse_url($proxy);
            if (is_array($elements)) {
                if ((isset($elements['scheme'])) and (isset($elements['host']))) {
                    $this->_proxy = $elements['scheme'] . '://' . $elements['host'];
                }
                if (isset($elements['port'])) {
                    $this->_proxy = $this->_proxy . ':' . $elements['port'];
                }
                if ((isset($elements['user'])) and (isset($elements['pass']))) {
                    $this->_proxyAuth = $elements['user'] . ':' . $elements['pass'];
                }
            }
        }
        if (isset($params['sslverify'])) {
            $this->_sslverify = $params['sslverify'];
        }
        if (isset($params['connectionTimeout'])) {
            $this->_connectionTimeout = $params['connectionTimeout'];
        }
        if (isset($params['httpRequest']) && $params['httpRequest'] instanceof HTTP_Request2) {
            $this->_httpRequest = $params['httpRequest'];
        }
    }

    /**
     * Body field getter.
     *
     * @return string body value
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * PostData field setter.
     *
     * @param string $value postData value
     */
    public function setPostData($value)
    {
        $this->_postData = $value;
    }

    /**
     * Sends the request.
     *
     * @return mixed PEAR error on error, true otherwise
     */
    public function sendRequest()
    {
        if (is_null($this->_httpRequest)) {
            $this->_httpRequest = new HTTP_Request2(
                $this->_uri,
                HTTP_Request2::METHOD_POST
            );
        }

        $request = $this->_httpRequest;
        $request->setUrl($this->_uri);
        $request->setMethod(HTTP_Request2::METHOD_POST);
        if ($this->_proxy !== null) {
            $elements = parse_url($this->_proxy);
            if (is_array($elements)) {
                if ((isset($elements['scheme'])) and (isset($elements['host']))) {
                    $request->setConfig('proxy_host', $elements['host']);
                }
                if (isset($elements['port'])) {
                    $request->setConfig('proxy_port', $elements['port']);
                }
                if ((isset($elements['user'])) and (isset($elements['pass']))) {
                    $request->setConfig('proxy_user', $elements['user']);
                    $request->setConfig('proxy_password', $elements['pass']);
                }
            }
        }
        $request->setConfig('ssl_verify_peer', $this->_sslverify);
        $request->setConfig('ssl_verify_host', $this->_sslverify);
        $request->setHeader('Content-type: text/xml; charset=' . $this->_encoding);
        $request->setHeader('User-Agent: PEAR::XML_RPC2/@package_version@');
        $request->setBody($this->_postData);
        if (isset($this->_connectionTimeout)) {
            $request->setConfig(
                'timeout',
                (int) ($this->_connectionTimeout / 1000)
            );
        }

        try {
            $result = $request->send();
            if ($result->getStatus() != 200) {
                throw new XML_RPC2_Exception_ReceivedInvalidStatusCode(
                    'Received non-200 HTTP Code: ' . $result->getStatus()
                    . '. Response body:' . $result->getBody()
                );
            }
        } catch (HTTP_Request2_Exception $e) {
            throw new XML_RPC2_Exception_Curl($e);
        }
        $this->_body = $result->getBody();

        return $result->getBody();
    }
}
