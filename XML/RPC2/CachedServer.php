<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (c) 2004 Sérgio Gonçalves Carvalho                                |
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
 * | Author: Sérgio Carvalho <sergio.carvalho@portugalmail.com>                  |
 * +-----------------------------------------------------------------------------+.
 *
 * @category  XML
 *
 * @author    Fabien MARTY <fab@php.net>
 * @copyright 2005-2006 Fabien MARTY
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see      http://pear.php.net/package/XML_RPC2
 */

/**
 * XML_RPC "cached server" class.
 *
 * @category  XML
 *
 * @author    Fabien MARTY <fab@php.net>
 * @copyright 2005-2006 Fabien MARTY
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_CachedServer
{
    /**
     * Whether or not to cache by default.
     *
     * @var bool
     */
    private $_cacheByDefault = true;

    /**
     * Cache_Lite object.
     *
     * @var object
     */
    private $_cacheObject;

    private array $_cacheOptions = [];
    /**
     * XML_RPC2_Server object (if needed, dynamically built).
     *
     * @var object
     */
    private $_serverObject;

    /**
     * Default cache group for XML_RPC server caching.
     *
     * @var string
     */
    private $_defaultCacheGroup = 'xml_rpc2_server';

    /**
     * The call handler is responsible for executing the server exported methods.
     *
     * @var mixed
     */
    private $_callHandler;

    /**
     * Either a class name or an object instance.
     *
     * @var mixed
     */
    private $_callTarget = '';

    /**
     * Methods prefix.
     *
     * @var string
     */
    private $_prefix = '';

    /**
     * XML_RPC2_Server options.
     *
     * @var array
     */
    private $_options = [];

    /**
     * Flag for debugging the caching process.
     *
     * @var bool
     */
    private $_cacheDebug = false;

    /**
     * Encoding.
     *
     * @var string
     */
    private $_encoding = 'utf-8';

    /**
     * Constructor.
     *
     * @param object $callTarget the call handler will receive a method call for each remote call received
     * @param array  $options    cache options
     */
    protected function __construct($callTarget, $options = [])
    {
        if (isset($options['cacheOptions'])) {
            $cacheOptions = $options['cacheOptions'];
            $this->_setCacheOptions($cacheOptions);
            unset($options['cacheOptions']);
        }
        if (isset($options['cacheDebug'])) {
            $this->_cacheDebug = $options['cacheDebug'];
            unset($options['cacheDebug']); // 'cacheDebug' is not a standard option for XML/RPC2/Server
        }
        $this->_options = $options;
        $this->_callTarget = $callTarget;
        if (isset($this->_options['encoding'])) {
            $this->_encoding = $this->_options['encoding'];
        }
        if (isset($this->_options['prefix'])) {
            $this->_prefix = $this->_options['prefix'];
        }
    }

    /**
     * Set options for the caching process.
     *
     * See Cache_Lite constructor for options
     * Specific options are 'cachedMethods', 'notCachedMethods', 'cacheByDefault', 'defaultCacheGroup'
     * See corresponding properties for more informations
     *
     * @param array $array the cache options
     */
    private function _setCacheOptions($array)
    {
        if (isset($array['defaultCacheGroup'])) {
            $this->_defaultCacheGroup = $array['defaultCacheGroup'];
            unset($array['defaultCacheGroup']); // this is a "non standard" option for Cache_Lite
        }
        if (isset($array['cacheByDefault'])) {
            $this->_cacheByDefault = $array['cacheByDefault'];
            unset($array['CacheByDefault']); // this is a "non standard" option for Cache_Lite
        }
        $array['automaticSerialization'] = false; // datas are already serialized in this class
        if (!isset($array['lifetime'])) {
            $array['lifetime'] = 3600; // we need a default lifetime
        }
        $this->_cacheOptions = $array;
        $this->_cacheObject = new Cache_Lite($this->_cacheOptions);
    }

    /**
     * "Emulated Factory" method to get the same API than XML_RPC2_Server class.
     *
     * Here, simply returns a new instance of XML_RPC2_CachedServer class
     *
     * @param mixed $callTarget either a class name or an object instance
     * @param array $options    associative array of options
     *
     * @return object a server class instance
     */
    public static function create($callTarget, $options = [])
    {
        return new XML_RPC2_CachedServer($callTarget, $options);
    }

    /**
     * Handle XML_RPC calls.
     */
    public function handleCall()
    {
        $response = $this->getResponse();
        $encoding = 'utf-8';
        if (isset($this->_options['encoding'])) {
            $encoding = $this->_options['encoding'];
        }
        header('Content-type: text/xml; charset=' . $encoding);
        header('Content-length: ' . $this->getContentLength($response));
        echo $response;
    }

    /**
     * Get the XML response of the XMLRPC server.
     *
     * @return string the XML response
     */
    public function getResponse()
    {
        if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            $methodName = $this->_parseMethodName($GLOBALS['HTTP_RAW_POST_DATA']);
        } else {
            $methodName = null;
        }
        $weCache = $this->_cacheByDefault;
        $lifetime = $this->_cacheOptions['lifetime'];
        if ($this->_cacheDebug) {
            if ($weCache) {
                echo "CACHE DEBUG : default values  => weCache=true, lifetime={$lifetime}\n";
            } else {
                echo "CACHE DEBUG : default values  => weCache=false, lifetime={$lifetime}\n";
            }
        }
        if ($methodName) {
            // work on reflection API to search for @xmlrpc.caching tags into PHPDOC comments
            [$weCache, $lifetime] = $this->_reflectionWork($methodName);
            if ($this->_cacheDebug) {
                if ($weCache) {
                    echo "CACHE DEBUG : phpdoc comments => weCache=true, lifetime={$lifetime}\n";
                } else {
                    echo "CACHE DEBUG : phpdoc comments => weCache=false, lifetime={$lifetime}\n";
                }
            }
        }
        if ($weCache and ($lifetime != -1)) {
            if (isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
                $cacheId = $this->_makeCacheId($GLOBALS['HTTP_RAW_POST_DATA']);
            } else {
                $cacheId = 'norawpostdata';
            }
            $this->_cacheObject = new Cache_Lite($this->_cacheOptions);
            $this->_cacheObject->setLifetime($lifetime);
            if ($data = $this->_cacheObject->get($cacheId, $this->_defaultCacheGroup)) {
                // cache id hit
                if ($this->_cacheDebug) {
                    echo "CACHE DEBUG : cache is hit !\n";
                }
            } else {
                // cache is not hit
                if ($this->_cacheDebug) {
                    echo "CACHE DEBUG : cache is not hit !\n";
                }
                $data = $this->_workWithoutCache();
                $this->_cacheObject->save($data);
            }
        } else {
            if ($this->_cacheDebug) {
                echo "CACHE DEBUG : we don't cache !\n";
            }
            $data = $this->_workWithoutCache();
        }

        return $data;
    }

    /**
     * Work on reflection API to search for @xmlrpc.caching tags into PHPDOC comments.
     *
     * @param string $methodName method name
     *
     * @return array an array with two fields -- (boolean) weCache and
     *               (int) lifetime. These are the parameters to use for
     *               caching
     */
    private function _reflectionWork($methodName)
    {
        $weCache = $this->_cacheByDefault;
        $lifetime = $this->_cacheOptions['lifetime'];
        if (is_string($this->_callTarget)) {
            $className = mb_strtolower($this->_callTarget);
        } else {
            $className = $this->_callTarget::class;
        }
        $class = new ReflectionClass($className);
        $method = $class->getMethod($methodName);
        $docs = explode("\n", $method->getDocComment());
        foreach ($docs as $doc) {
            $doc = trim($doc, " \r\t/*");
            $res = preg_match('/@xmlrpc.caching ([+-]{0,1}[a-zA-Z0-9]*)/', $doc, $results); // TODO : better/faster regexp ?
            if ($res > 0) {
                $value = $results[1];
                if (($value == 'yes') or ($value == 'true') or ($value == 'on')) {
                    $weCache = true;
                } elseif (($value == 'no') or ($value == 'false') or ($value == 'off')) {
                    $weCache = false;
                } else {
                    $lifetime = (int) $value;
                    if ($lifetime == -1) {
                        $weCache = false;
                    } else {
                        $weCache = true;
                    }
                }
            }
        }

        return [$weCache, $lifetime];
    }

    /**
     * Parse the method name from the raw XMLRPC client request.
     *
     * NB : the prefix is removed from the method name
     *
     * @param string $request raw XMLRPC client request
     *
     * @return string method name
     */
    private function _parseMethodName($request)
    {
        // TODO : change for "simplexml"
        $res = preg_match('/<methodName>' . $this->_prefix . '([a-zA-Z0-9\.,\/]*)<\/methodName>/', $request, $results);
        if ($res > 0) {
            return $results[1];
        }

        return false;
    }

    /**
     * Do the real stuff if no cache available.
     *
     * @return string the response of the real XML/RPC2 server
     */
    private function _workWithoutCache()
    {
        $this->_serverObject = XML_RPC2_Server::create($this->_callTarget, $this->_options);

        return $this->_serverObject->getResponse();
    }

    /**
     * Make a cache id depending on the raw xmlrpc client request but depending on "environnement" setting too.
     *
     * @param string $raw_request the raw request data
     *
     * @return string cache id
     */
    private function _makeCacheId($raw_request)
    {
        return md5($raw_request . serialize($this->_options));
    }

    /**
     * Clean all the cache.
     */
    public function clean()
    {
        $this->_cacheObject->clean($this->_defaultCacheGroup, 'ingroup');
    }

    /**
     * Gets the content legth of a serialized XML-RPC message in bytes.
     *
     * @param string $content the serialized XML-RPC message
     *
     * @return int the content length in bytes
     */
    protected function getContentLength($content)
    {
        return mb_strlen($content, '8bit');
    }
}
