<?php
/**
 * @package     cedThumbnails
 * @subpackage  plg_content_relatedthumbarticles
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();

/**
 * Renders a SQL element
 *
 * @package     Joomla.Framework
 * @subpackage        Parameter
 * @since        1.5
 */

class JElementRelatedArticles extends JElement
{
    /**
     * Element name
     *
     * @access    protected
     * @var        string
     */
    var $_name = 'relatedarticles';

    function fetchElement($name, $value, &$node, $control_name)
    {
        $db = & JFactory::getDbo();
        $db->setQuery($node->attributes('query'));
        $key = ($node->attributes('key_field') ? $node->attributes('key_field') : 'value');
        $val = ($node->attributes('value_field') ? $node->attributes('value_field') : $name);

        if ($node->attributes('multiple')) {
            $size = $node->attributes('size') ? $node->attributes('size') : '5';
            $multiple = ' multiple="multiple" size="' . $size . '"';
            $multipleArray = "[]";
        } else {
            $multiple = '';
            $multipleArray = '';
        }
        $attributes = 'class="inputbox" ' . $multiple;
        $options = $db->loadObjectList();

        return JHTML::_('select.genericlist', $options, '' . $control_name . '[' . $name . ']' . $multipleArray, $attributes, $key, $val, $value, $control_name . $name);
    }
}
