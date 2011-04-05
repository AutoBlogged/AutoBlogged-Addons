<?php
/* vim: set expandtab tabstop=4 shiftwidth=4: */
// +----------------------------------------------------------------------+
// | PHP version 4                                                        |
// +----------------------------------------------------------------------+
// | Copyright (c) 1997-2006 The PHP Group                                |
// +----------------------------------------------------------------------+
// | This source file is subject to version 3.0 of the PHP license,       |
// | that is bundled with this package in the file LICENSE, and is        |
// | available through the world-wide-web at the following url:           |
// | http://www.php.net/license/3_0.txt.                                  |
// | If you did not receive a copy of the PHP license and are unable to   |
// | obtain it through the world-wide-web, please send a note to          |
// | license@php.net so we can mail you a copy immediately.               |
// +----------------------------------------------------------------------+
// | Authors: Frederic Poeydomenge <fpoeydomenge@free.fr>                 |
// +----------------------------------------------------------------------+

/**
 * Wrapper for the var_dump function.
 *
 * " The var_dump function displays structured information about expressions
 * that includes its type and value. Arrays are explored recursively
 * with values indented to show structure. "
 *
 * The Var_Dump class captures the output of the var_dump function,
 * by using output control functions, and then uses external renderer
 * classes for displaying the result in various graphical ways :
 * simple text, HTML/XHTML text, HTML/XHTML table, XML, ...
 *
 * @category  PHP
 * @package   Var_Dump
 * @author    Frederic Poeydomenge <fpoeydomenge@free.fr>
 * @copyright 1997-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   CVS: $Id: Common.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

/**
 * A base class for Var_Dump renderers, must be inherited by all such.
 *
 * All common methods are declared here. If a given renderer contains
 * a particular method, that method will overload the one here.
 *
 * @category  PHP
 * @package   Var_Dump
 * @author    Frederic Poeydomenge <fpoeydomenge@free.fr>
 * @copyright 1997-2006 The PHP Group
 * @license   http://www.php.net/license/3_0.txt PHP License 3.0
 * @version   CVS: $Id: Common.php 233111 2007-04-02 09:38:10Z fredericpoeydome $
 * @link      http://pear.php.net/package/Var_Dump
 */

class Var_Dump_Renderer_Common
{

    /**
     * Run-time configuration options.
     *
     * @var array
     * @access public
     */
    var $options = array();

    /**
     * Default configuration options.
     *
     * See Var_Dump/Renderer/*.php for the complete list of options
     *
     * @var array
     * @access public
     */
    var $defaultOptions = array();

    /**
     * Array containing the element family : start/finish group, start/finish element
     *
     * @var array
     * @access public
     */
    var $family;

    /**
     * Array containing the element depths
     *
     * @var array
     * @access public
     */
    var $depth;

    /**
     * Array containing the element types
     *
     * @var array
     * @access public
     */
    var $type;

    /**
     * Array containing the element values
     *
     * @var array
     * @access public
     */
    var $value;

    /**
     * Array containing the strlen of keys for all the nested arrays
     *
     * @var array
     * @access public
     */
    var $keyLen;

    /**
     * Set run-time configuration options for the renderer
     *
     * @param array $options Run-time configuration options.
     * @access public
     */
    function setOptions($options = array())
    {
        $this->options = array_merge($this->defaultOptions, $options);
    }

    /**
     * Initialize internal data structures for the rendering.
     *
     * @param array $family Containing the element family.
     * @param array $depth Containing the element depths.
     * @param array $type Containing the element types.
     * @param array $value Containing the element values.
     * @param array $keyLen Strlen of keys for all the nested arrays
     * @access public
     */
    function initialize(& $family, & $depth, & $type, & $value, & $keyLen)
    {
        $this->family = $family;
        $this->depth  = $depth;
        $this->type   = $type;
        $this->value  = $value;
        $this->keyLen = $keyLen;
    }

    /**
     * Returns the string representation of a variable
     *
     * @return string The string representation of the variable.
     * @access public
     * @abstract
     */
    function toString()
    {
    }

}

?>