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
 * Class representing an XML-RPC exported method.
 *
 * This class is used internally by XML_RPC2_Server. External users of the
 * package should not need to ever instantiate XML_RPC2_Server_Method
 *
 * @category  XML
 *
 * @author    Sergio Carvalho <sergio.carvalho@portugalmail.com>
 * @copyright 2004-2006 Sergio Carvalho
 * @license   http://www.gnu.org/copyleft/lesser.html  LGPL License 2.1
 *
 * @see       https://pear.php.net/package/XML_RPC2
 */
class XML_RPC2_Server_Method
{
    /**
     * Method signature parameters.
     *
     * @var array
     */
    private $_parameters;

    /**
     * Method signature return type.
     *
     * @var string
     */
    private $_returns;

    /**
     * Method help, for introspection.
     *
     * @var string
     */
    private $_help;

    /**
     * InternalMethod field - method name in PHP-land.
     *
     * @var string
     */
    private $_internalMethod;

    /**
     * Hidden field.
     *
     * True if the method is hidden
     *
     * @var bool
     */
    private $_hidden;

    /**
     * External method name.
     *
     * @var string
     */
    private $_name;

    /**
     * Number of required parameters.
     *
     * @var int
     */
    private $_numberOfRequiredParameters;

    /**
     * Create a new XML-RPC method by introspecting a PHP method.
     *
     * @param ReflectionMethod $method        the PHP method to introspect
     * @param string           $defaultPrefix default prefix
     */
    public function __construct(ReflectionMethod $method, $defaultPrefix)
    {
        $hidden = false;
        $docs = $method->getDocComment();
        if (!$docs) {
            $hidden = true;
        }
        $docs = explode("\n", $docs);

        $parameters = [];
        $methodname = null;
        $returns = 'mixed';
        $shortdesc = '';
        $paramcount = -1;
        $prefix = $defaultPrefix;

        // Extract info from Docblock
        $paramDocs = [];
        foreach ($docs as $doc) {
            $doc = trim($doc, " \r\t/*");
            if ($doc != '' && mb_strpos($doc, '@') !== 0) {
                if ($shortdesc) {
                    $shortdesc .= "\n";
                }
                $shortdesc .= $doc;

                continue;
            }
            if (mb_strpos($doc, '@xmlrpc.hidden') === 0) {
                $hidden = true;
            }
            if ((mb_strpos($doc, '@xmlrpc.prefix') === 0) && preg_match('/@xmlrpc.prefix( )*(.*)/', $doc, $matches)) {
                $prefix = $matches[2];
            }
            if ((mb_strpos($doc, '@xmlrpc.methodname') === 0) && preg_match('/@xmlrpc.methodname( )*(.*)/', $doc, $matches)) {
                $methodname = $matches[2];
            }
            if (mb_strpos($doc, '@param') === 0) { // Save doctag for usage later when filling parameters
                $paramDocs[] = $doc;
            }

            if (mb_strpos($doc, '@return') === 0) {
                $param = preg_split('/\s+/', $doc);
                if (isset($param[1])) {
                    $param = $param[1];
                    $returns = $param;
                }
            }
        }
        $this->_numberOfRequiredParameters = $method->getNumberOfRequiredParameters(); // we don't use isOptional() because of bugs in the reflection API
        // Fill in info for each method parameter
        foreach ($method->getParameters() as $parameterIndex => $parameter) {
            // Parameter defaults
            $newParameter = ['type' => 'mixed'];

            // Attempt to extract type and doc from docblock
            if (array_key_exists($parameterIndex, $paramDocs)
                && preg_match('/@param\s+(\S+)(\s+(.+))/', $paramDocs[$parameterIndex], $matches)
            ) {
                if (mb_strpos($matches[1], '|')) {
                    $newParameter['type'] = XML_RPC2_Server_Method::_limitPHPType(explode('|', $matches[1]));
                } else {
                    $newParameter['type'] = XML_RPC2_Server_Method::_limitPHPType($matches[1]);
                }
                $tmp = '$' . $parameter->getName() . ' ';
                if (mb_strpos($matches[3], '$' . $tmp) === 0) {
                    $newParameter['doc'] = $matches[3];
                } else {
                    // The phpdoc comment is something like "@param string $param description of param"
                    // Let's keep only "description of param" as documentation (remove $param)
                    $newParameter['doc'] = mb_substr($matches[3], mb_strlen($tmp));
                }
                $newParameter['doc'] = preg_replace('_^\s*_', '', $newParameter['doc']);
            }

            $parameters[$parameter->getName()] = $newParameter;
        }

        if (is_null($methodname)) {
            $methodname = $prefix . $method->getName();
        }

        $this->_internalMethod = $method->getName();
        $this->_parameters = $parameters;
        $this->_returns = $returns;
        $this->_help = $shortdesc;
        $this->_name = $methodname;
        $this->_hidden = $hidden;
    }

    /**
     * InternalMethod getter.
     *
     * @return string internalMethod
     */
    public function getInternalMethod()
    {
        return $this->_internalMethod;
    }

    /**
     * Hidden getter.
     *
     * @return bool hidden value
     */
    public function isHidden()
    {
        return $this->_hidden;
    }

    /**
     * Name getter.
     *
     * @return string name
     */
    public function getName()
    {
        return $this->_name;
    }

    /**
     * Check if method matches provided call signature.
     *
     * Compare the provided call signature with this methods' signature and
     * return true iff they match.
     *
     * @param string $methodName Signature to compare method name
     * @param array  $callParams array of parameter values for method call
     *
     * @return bool True if call matches signature, false otherwise
     */
    public function matchesSignature($methodName, $callParams)
    {
        if ($methodName != $this->_name) {
            return false;
        }
        if (count($callParams) < $this->_numberOfRequiredParameters) {
            return false;
        }
        if (count($callParams) > $this->_parameters) {
            return false;
        }
        $paramIndex = 0;
        foreach ($this->_parameters as $param) {
            $paramIndex++;
            if ($paramIndex <= $this->_numberOfRequiredParameters) {
                // the parameter is not optional
                $callParamType = XML_RPC2_Server_Method::_limitPHPType(gettype($callParams[$paramIndex - 1]));
                if ((!($param['type'] == 'mixed')) and ($param['type'] != $callParamType)) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Return a HTML signature of the method.
     *
     * @return string HTML signature
     */
    public function getHTMLSignature()
    {
        $name = $this->_name;
        $returnType = $this->_returns;
        $result = "<span class=\"type\">({$returnType})</span> ";
        $result .= "<span class=\"name\">{$name}</span>";
        $result .= '<span class="other">(</span>';
        $first = true;
        $nbr = 0;
        foreach ($this->_parameters as $name => $parameter) {
            $nbr++;
            if ($nbr == $this->_numberOfRequiredParameters + 1) {
                $result .= '<span class="other"> [ </span>';
            }
            if ($first) {
                $first = false;
            } else {
                $result .= ', ';
            }
            $type = $parameter['type'];
            $result .= "<span class=\"paratype\">({$type}) </span>";
            $result .= "<span class=\"paraname\">{$name}</span>";
        }
        if ($nbr > $this->_numberOfRequiredParameters) {
            $result .= '<span class="other"> ] </span>';
        }
        $result .= '<span class="other">)</span>';

        return $result;
    }

    /**
     * Print a complete HTML description of the method.
     */
    public function autoDocument()
    {
        $name = $this->getName();
        $signature = $this->getHTMLSignature();
        $id = md5($name);
        $help = nl2br(htmlentities($this->_help, ENT_COMPAT));
        echo "      <h3><a name=\"{$id}\">{$signature}</a></h3>\n";
        echo "      <p><b>Description :</b></p>\n";
        echo "      <div class=\"description\">\n";
        echo "        {$help}\n";
        echo "      </div>\n";
        if (count($this->_parameters) > 0) {
            echo "      <p><b>Parameters : </b></p>\n";
            if (count($this->_parameters) > 0) {
                echo "      <table>\n";
                echo "        <tr><td><b>Type</b></td><td><b>Name</b></td><td><b>Documentation</b></td></tr>\n";
                foreach ($this->_parameters as $name => $parameter) {
                    $type = $parameter['type'];
                    $doc = isset($parameter['doc']) ? htmlentities($parameter['doc'], ENT_COMPAT) : 'Method is not documented. No PHPDoc block was found associated with the method in the source code.';
                    echo "        <tr><td>{$type}</td><td>{$name}</td><td>{$doc}</td></tr>\n";
                }
                echo "      </table>\n";
            }
        }
    }

    /**
     * Standardize type names between gettype php function and phpdoc comments (and limit to xmlrpc available types).
     *
     * @param string $type the parameter type
     *
     * @return string standardized type
     */
    private static function _limitPHPType($type)
    {
        $tmp = mb_strtolower($type);

        return match ($tmp) {
            'array'            => 'array',
            'assoc'            => 'array',
            'base64'           => 'string',
            'bool'             => 'boolean',
            'boolean'          => 'boolean',
            'char'             => 'string',
            'datetime'         => 'mixed',
            'datetime.iso8601' => 'mixed',
            'double'           => 'double',
            'float'            => 'double',
            'i4'               => 'integer',
            'int'              => 'integer',
            'integer'          => 'integer',
            'iso8601'          => 'mixed',
            'str'              => 'string',
            'string'           => 'string',
            'struct'           => 'array',
            'structure'        => 'array',
            default            => 'mixed'
        };
    }
}
