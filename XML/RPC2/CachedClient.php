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
 * XML_RPC "cached client" class.
 *
 * @category  XML
 *
 * @author    Fabien MARTY <fab@php.net>
 * @copyright 2005-2006 Fabien MARTY
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_CachedClient
{
    /**
     * Associative array of options for XML_RPC2_Client.
     *
     * @var array
     */
    private $_options;

    /**
     * Holds the uri for the XML_RPC server.
     *
     * @var array
     */
    private $_uri;

    /**
     * Holds the debug flag.
     *
     * @var bool
     */
    private $_debug = false;

    /**
     * Cache_Lite options array.
     *
     * @var array
     */
    private $_cacheOptions = [];

    /**
     * Cached methods array (usefull only if cache is off by default).
     *
     * Example1 : array('method1ToCache', 'method2ToCache', ...)
     * Example2 (with specific cache lifetime) :
     * array('method1ToCache' => 3600, 'method2ToCache' => 60, ...)
     * NB : a lifetime value of -1 means "no cache for this method"
     *
     * @var array
     */
    private $_cachedMethods = [];

    /**
     * Non-Cached methods array (usefull only if cache is on by default).
     *
     * Example : array('method1ToCache', 'method2ToCache', ...)
     *
     * @var array
     */
    private $_notCachedMethods = [];

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

    /**
     * XML_RPC2_Client object (if needed, dynamically built).
     *
     * @var object
     */
    private $_clientObject;

    /**
     * Default cache group for XML_RPC client caching.
     *
     * @var string
     */
    private $_defaultCacheGroup = 'xml_rpc2_client';

    /**
     * "cache debug" flag (for debugging the caching process).
     *
     * @var bool
     */
    private $_cacheDebug = false;

    /**
     * Constructor.
     *
     * TODO : documentations about cache options
     *
     * @param string $uri     URI for the XML-RPC server
     * @param array  $options (optional) Associative array of options
     */
    protected function __construct($uri, $options = [])
    {
        if (isset($options['cacheOptions'])) {
            $array = $options['cacheOptions'];
            if (isset($array['defaultCacheGroup'])) {
                $this->_defaultCacheGroup = $array['defaultCacheGroup'];
                unset($array['defaultCacheGroup']); // this is a "non standard" option for Cache_Lite
            }
            if (isset($array['cachedMethods'])) {
                $this->_cachedMethods = $array['cachedMethods'];
                unset($array['cachedMethods']); // this is a "non standard" option for Cache_Lite
            }
            if (isset($array['notCachedMethods'])) {
                $this->_notCachedMethods = $array['notCachedMethods'];
                unset($array['notCachedMethods']); // this is a "non standard" option for Cache_Lite
            }
            if (isset($array['cacheByDefault'])) {
                $this->_cacheByDefault = $array['cacheByDefault'];
                unset($array['CacheByDefault']); // this is a "non standard" option for Cache_Lite
            }
            $array['automaticSerialization'] = false; // datas are already serialized in this class
            if (!isset($array['lifetime'])) {
                $array['lifetime'] = 3600; // we need a default lifetime
            }
            unset($options['cacheOptions']); // this is a "non standard" option for XML/RPC2/Client
        } else { // no cache options ?
            $array = [
                'lifetime'               => 3600,  // we need a default lifetime
                'automaticSerialization' => false, // datas are already serialized in this class
            ];
        }
        if (isset($options['cacheDebug'])) {
            $this->_cacheDebug = $options['cacheDebug'];
            unset($options['cacheDebug']); // this a "non standard" option for XML/RPC2/Client
        }
        $this->_cacheOptions = $array;
        $this->_cacheObject = new Cache_Lite($this->_cacheOptions);
        $this->_options = $options;
        $this->_uri = $uri;
    }

    /**
     * "Emulated Factory" method to get the same API than XML_RPC2_Client class.
     *
     * Here, simply returns a new instance of XML_RPC2_CachedClient class
     *
     * @param string $uri     URI for the XML-RPC server
     * @param array  $options client options
     *
     * @return XML_RPC2_CachedClient the new client
     */
    public static function create($uri, $options = [])
    {
        return new XML_RPC2_CachedClient($uri, $options);
    }

    /**
     * Catchall method.
     *
     * Encapsulate all the class logic :
     * - determine if the cache has to be used (or not) for the called method
     * - see if a cache is available for this call
     * - if no cache available, really do the call and store the result for next time
     *
     * @param string $methodName Method name
     * @param array  $parameters Parameters
     *
     * @return mixed The call result, already decoded into native types
     */
    public function __call($methodName, $parameters)
    {
        if (!isset($this->_cacheObject)) {
            $this->_cacheObject = new Cache_Lite($this->_cacheOptions);
        }
        if (in_array($methodName, $this->_notCachedMethods)) {
            // if the called method is listed in _notCachedMethods => no cache
            if ($this->_cacheDebug) {
                echo "CACHE DEBUG : the called method is listed in _notCachedMethods => no cache !\n";
            }

            return $this->_workWithoutCache___($methodName, $parameters);
        }
        if (!$this->_cacheByDefault) {
            if ((!(isset($this->_cachedMethods[$methodName]))) and (!in_array($methodName, $this->_cachedMethods))) {
                // if cache is not on by default and if the called method is not described in _cachedMethods array
                // => no cache
                if ($this->_cacheDebug) {
                    echo "CACHE DEBUG : cache is not on by default and the called method is not listed in _cachedMethods => no cache !\n";
                }

                return $this->_workWithoutCache___($methodName, $parameters);
            }
        }
        if (isset($this->_cachedMethods[$methodName])) {
            if ($this->_cachedMethods[$methodName] == -1) {
                // if a method is described with a lifetime value of -1 => no cache
                if ($this->_cacheDebug) {
                    echo "CACHE DEBUG : called method has a -1 lifetime value => no cache !\n";
                }

                return $this->_workWithoutCache___($methodName, $parameters);
            }
            // if a method is described with a specific (and <> -1) lifetime
            // => we fix this new lifetime
            $this->_cacheObject->setLifetime($this->_cachedMethods[$methodName]);
        } else {
            // there is no specific lifetime, let's use the default one
            $this->_cacheObject->setLifetime($this->_cacheOptions['lifetime']);
        }
        $cacheId = $this->_makeCacheId___($methodName, $parameters);
        $data = $this->_cacheObject->get($cacheId, $this->_defaultCacheGroup);
        if (is_string($data)) {
            // cache is hit !
            if ($this->_cacheDebug) {
                echo "CACHE DEBUG : cache is hit !\n";
            }

            return unserialize($data);
        }
        // the cache is not hit, let's call the "real" XML_RPC client
        if ($this->_cacheDebug) {
            echo "CACHE DEBUG : cache is not hit !\n";
        }
        $result = $this->_workWithoutCache___($methodName, $parameters);
        $this->_cacheObject->save(serialize($result)); // save in cache for next time...

        return $result;
    }

    /**
     * Do the real call if no cache available.
     *
     * NB : The '___' at the end of the method name is to avoid collisions with
     * XMLRPC __call()
     *
     * @param string $methodName Method name
     * @param array  $parameters Parameters
     *
     * @return mixed The call result, already decoded into native types
     */
    // @codingStandardsIgnoreLine
    private function _workWithoutCache___($methodName, $parameters)
    {
        if (!(isset($this->_clientObject))) {
            // If the XML_RPC2_Client object is not available, let's build it
            $this->_clientObject = XML_RPC2_Client::create($this->_uri, $this->_options);
        }

        // the real function call...
        return call_user_func_array([$this->_clientObject, $methodName], $parameters);
    }

    /**
     * Make a cache id depending on method called (and corresponding parameters) but depending on "environnement" setting too.
     *
     * NB : The '___' at the end of the method name is to avoid collisions with
     * XMLRPC __call()
     *
     * @param string $methodName called method
     * @param array  $parameters parameters of the called method
     *
     * @return string cache id
     */
    // @codingStandardsIgnoreLine
    private function _makeCacheId___($methodName, $parameters)
    {
        return md5($methodName . serialize($parameters) . serialize($this->_uri) . serialize($this->_options));
    }

    /**
     * Drop the cache file corresponding to the given method call.
     *
     * NB : The '___' at the end of the method name is to avoid collisions with
     * XMLRPC __call()
     *
     * @param string $methodName called method
     * @param array  $parameters parameters of the called method
     */
    // @codingStandardsIgnoreLine
    public function dropCacheFile___($methodName, $parameters)
    {
        $id = $this->_makeCacheId___($methodName, $parameters);
        $this->_cacheObject->remove($id, $this->_defaultCacheGroup);
    }

    /**
     * Clean all the cache.
     *
     * NB : The '___' at the end of the method name is to avoid collisions with
     * XMLRPC __call()
     */
    // @codingStandardsIgnoreLine
    public function clean___()
    {
        $this->_cacheObject->clean($this->_defaultCacheGroup, 'ingroup');
    }
}
