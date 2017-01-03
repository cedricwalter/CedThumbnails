<?php
/**
 * @package     cedThumbnails
 * @subpackage  com_cedthumbnails
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class CedThumbnailsViewFrontpage extends JViewLegacy
{
    /**
     * Display the view
     *
     * @return    mixed    False on error, null otherwise.
     */
    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        $version = (string)$this->getXml()->version;
        $isFree = strpos((string)$this->getXml()->version, 'free') !== false ? " " : " (Licensed) ";
        JToolBarHelper::title(JText::_('cedThumbnails '). $version. $isFree , 'tag.png');

        //Get Model
        $this->version = $version;
        $this->isFree = $isFree;

        $user = JFactory::getUser();
        if ($user->authorise('core.admin', 'com_cedthumbnails'))
        {
            JToolbarHelper::preferences('com_cedthumbnails');
        }

        parent::display($tpl);
    }

    /**
     * @return SimpleXMLElement
     */
    private function getXml()
    {
        $xmlFile = JPATH_ADMINISTRATOR . '/components/com_cedthumbnails/cedthumbnails.xml';
        $xml = simplexml_load_file($xmlFile, 'SimpleXMLElement');
        return $xml;
    }

}
