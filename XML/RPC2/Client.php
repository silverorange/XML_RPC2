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
 * XML_RPC client class. Use this class to access remote methods.
 *
 * To use this class, construct it providing the server URL and method prefix.
 * Then, call remote methods on the new instance as if they were local.
 *
 * Example:
 * <code>
 *  $client = XML_RPC2_Client('http://xmlrpc.example.com/1.0/', 'example.');
 *  $result = $client->hello('Sergio');
 *  print($result);
 * </code>
 *
 * The above example will call the example.hello method on the xmlrpc.example.com
 * server, under the /1.0/ URI.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
abstract class XML_RPC2_Client
{
    /**
     * URI Field (holds the uri for the XML_RPC server).
     *
     * @var string
     */
    protected $uri;

    /**
     * Proxy Field (holds the proxy server data).
     *
     * @var string
     */
    protected $proxy;

    /**
     * Holds the prefix to prepend to method names.
     *
     * @var string
     */
    protected $prefix;

    /**
     * Holds the debug flag.
     *
     * @var bool
     */
    protected $debug = false;

    /**
     * Hold the encoding of the client request.
     *
     * @var string
     */
    protected $encoding = 'utf-8';

    /**
     * Holds the escaping method(s) of the client request.
     *
     * @var string
     */
    protected $escaping = ['non-ascii', 'non-print', 'markup'];

    /**
     * Holds the SSL verify flag.
     *
     * @var bool
     */
    protected $sslverify = true;

    /**
     * Holds the connection timeout in milliseconds of the client request.
     *
     * @var int
     */
    protected $connectionTimeout;

    /**
     * Ugly hack flag to avoid http://bugs.php.net/bug.php?id=21949.
     *
     * See XML_RPC2_Backend_Xmlrpcext_Value::createFromNative() from more infos
     *
     * @var bool
     */
    protected $uglyStructHack = true;

    /**
     * Preconfigured HTTP_Request2 provided by the user.
     *
     * @var HTTP_Request2
     */
    protected $httpRequest;

    /**
     * Construct a new XML_RPC2_Client.
     *
     * To create a new XML_RPC2_Client, a URI must be provided (e.g. http://xmlrpc.example.com/1.0/).
     * Optionally, some options may be set as an associative array. Accepted keys are :
     * 'prefix', 'proxy', 'debug' => see correspondant property to get more informations
     * 'encoding' => The XML encoding for the requests (optional, defaults to utf-8)
     * 'sslverify' => boolean, true iff SSL certificates are to be verified against local cert database (optional, default false)
     * 'connectionTimeout' => Timeout, in seconds, for the XML-RPC HTTP request (optional, default is no timeout)
     * 'httpRequest' => Preconfigured HTTP_Request2 instance to be used in executing the XML-RPC calls (optional)
     *
     * @param string $uri     URI for the XML-RPC server
     * @param array  $options (optional) Associative array of options
     */
    protected function __construct($uri, $options = [])
    {
        if (!$uriParse = parse_url($uri)) {
            throw new XML_RPC2_Exception_InvalidUri(sprintf('Client URI \'%s\' is not valid', $uri));
        }
        $this->uri = $uri;
        if (isset($options['prefix'])) {
            if (!XML_RPC2_ClientHelper::testMethodName($options['prefix'])) {
                throw new XML_RPC2_Exception_InvalidPrefix(sprintf('Prefix \'%s\' is not valid', $options['prefix']));
            }
            $this->prefix = $options['prefix'];
        }
        if (isset($options['proxy'])) {
            if (!$proxyParse = parse_url($options['proxy'])) {
                throw new XML_RPC2_Exception_InvalidProxy(sprintf('Proxy URI \'%s\' is not valid', $options['proxy']));
            }
            $this->proxy = $options['proxy'];
        }
        if (isset($options['debug'])) {
            if (!is_bool($options['debug'])) {
                throw new XML_RPC2_Exception_InvalidDebug(sprintf('Debug \'%s\' is not valid', $options['debug']));
            }
            $this->debug = $options['debug'];
        }
        if (isset($options['encoding'])) {
            // TODO : control & exception
            $this->encoding = $options['encoding'];
        }
        if (isset($options['escaping'])) {
            // TODO : control & exception
            $this->escaping = $options['escaping'];
        }
        if (isset($options['uglyStructHack'])) {
            $this->uglyStructHack = $options['uglyStructHack'];
        }
        if (isset($options['sslverify'])) {
            if (!is_bool($options['sslverify'])) {
                throw new XML_RPC2_Exception_InvalidSslVerify(sprintf('SSL verify \'%s\' is not valid', $options['sslverify']));
            }
            $this->sslverify = $options['sslverify'];
        }
        if (isset($options['connectionTimeout'])) {
            if (!is_int($options['connectionTimeout'])) {
                throw new XML_RPC2_Exception_InvalidConnectionTimeout(sprintf('Connection timeout \'%s\' is not valid', $options['connectionTimeout']));
            }
            $this->connectionTimeout = $options['connectionTimeout'];
        }
        if (isset($options['httpRequest'])) {
            $this->httpRequest = $options['httpRequest'];
        }
    }

    /**
     * Factory method to select, create and return a XML_RPC2_Client backend.
     *
     * To create a new XML_RPC2_Client, a URI must be provided (e.g. http://xmlrpc.example.com/1.0/).
     *
     * Optionally, some options may be set.
     *
     * @param string $uri     URI for the XML-RPC server
     * @param array  $options (optional) associative array of options (see constructor)
     *
     * @return XML_RPC2_Client new client using requested backend
     */
    public static function create($uri, $options = [])
    {
        // As of PHP 8.0, $this is not set, even if you called $obj->create()
        // so this condition is always false, and can be removed
        //        if (isset($this)) { // Method called non-statically forward to remote call() as RPC
        //            $this->__call('create', func_get_args());
        //        }
        if (isset($options['backend'])) {
            XML_RPC2_Backend::setBackend($options['backend']);
        }
        $backend = XML_RPC2_Backend::getClientClassname();

        return new $backend($uri, $options);
    }

    /**
     * __call Catchall. This method catches remote method calls and provides for remote forwarding.
     *
     * If the parameters are native types, this method will use XML_RPC_Value::createFromNative to
     * convert it into an XML-RPC type. Whenever a parameter is already an instance of XML_RPC_Value
     * it will be used as provided. It follows that, in situations when XML_RPC_Value::createFromNative
     * proves inacurate -- as when encoding DateTime values -- you should present an instance of
     * XML_RPC_Value in lieu of the native parameter.
     *
     * @param string $methodName Method name
     * @param array  $parameters Parameters
     *
     * @return mixed The call result, already decoded into native types
     */
    abstract public function __call($methodName, $parameters);
}
