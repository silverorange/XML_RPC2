<?php

/**
 * +-----------------------------------------------------------------------------+
 * | Copyright (c) 2004-2006 Sergio Gonalves Carvalho                                |
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
 * Class that feeds XML_RPC2 with input originating from.
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2011 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Server_Input_RawPostData implements XML_RPC2_Server_Input
{
    /**
     * The raw post data of the request.
     *
     * @var string
     */
    protected $input;

    /**
     * Return true if there is no input (input is empty).
     *
     * @return bool True iff there is no input
     */
    public function isEmpty()
    {
        if (!isset($this->input)) {
            $this->readRequest();
        }

        return empty($this->input);
    }

    /**
     * Return the input as a string.
     *
     * @return string The Input
     */
    public function readRequest()
    {
        if (!isset($this->input) && !isset($GLOBALS['HTTP_RAW_POST_DATA'])) {
            throw new XML_RPC2_Exception_Config(
                'XML_RPC2_Server_Input_RawPostData requested but PHP config '
                . 'does not show GLOBALS[\'HTTP_RAW_POST_DATA\'] as available'
            );
        }

        if (!isset($this->input)) {
            $this->input = $GLOBALS['HTTP_RAW_POST_DATA'];
        }

        return $this->input;
    }
}
