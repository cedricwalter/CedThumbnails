<?php
/**
 * @package     CedThumbnails
 * @subpackage  com_cedthumbnails
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');
jimport('joomla.application.input');

class CedThumbnailsController extends JControllerLegacy
{
    protected $default_view = 'frontpage';

    public function display($cachable = false, $urlparams = false)
    {
        $view = JFactory::getApplication()->input->get('view');
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'frontpage');
        }
        return parent::display($cachable, $urlparams);
    }
}

