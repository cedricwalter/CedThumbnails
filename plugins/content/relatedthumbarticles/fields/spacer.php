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

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

/**
 * Form Field class for the Joomla Framework.
 *
 * @package        Joomla.Framework
 * @subpackage    Form
 * @since        1.6
 */
class JFormFieldSpacer extends JFormField
{
    /**
     * The form field type.
     *
     * @var        string
     * @since    1.6
     */
    protected $type = 'Spacer';

    /**
     * Method to get the field input markup.
     *
     * @return    string    The field input markup.
     * @since    1.6
     */
    protected function getInput()
    {
        return ' ';
    }

    /**
     * Method to get the field label markup.
     *
     * @return    string    The field label markup.
     * @since    1.6
     */
    protected function getLabel()
    {
        $html = array();
        $class = $this->element['class'] ? (string)$this->element['class'] : '';

        $html[] = '<div style="clear:left;" class="spacer-wrapper ' . $class . '">';
        if ((string)$this->element['hr'] == 'true') {
            $html[] = '<hr class="' . $class . '" />';
        } else {
            $label = '';
            // Get the label text from the XML element, defaulting to the element name.
            $text = $this->element['label'] ? (string)$this->element['label'] : (string)$this->element['name'];
            $text = $this->translateLabel ? JText::_($text) : $text;

            // Build the class for the label.
            $class = !empty($this->description) ? 'hasTip' : '';
            $class = $this->required == true ? $class . ' required' : $class;

            // Add the opening label tag and main attributes attributes.
            $label .= '<span id="' . $this->id . '-lbl" class="' . $class . '"';

            // If a description is specified, use it to build a tooltip.
            if (!empty($this->description)) {
                $label .= ' title="' . htmlspecialchars(trim($text, ':') . '::' .
                        ($this->translateDescription ? JText::_($this->description) : $this->description), ENT_COMPAT, 'UTF-8') . '"';
            }

            // Add the label text and closing tag.
            $label .= '>' . $text . '</span>';
            $html[] = $label;
        }
        $html[] = '</div>';
        return implode('', $html);
    }

    /**
     * Method to get the field title.
     *
     * @return    string    The field title.
     * @since    1.6
     */
    protected function getTitle()
    {
        return $this->getLabel();
    }
}
