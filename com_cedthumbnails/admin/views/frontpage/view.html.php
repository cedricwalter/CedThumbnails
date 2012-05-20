<?php
/**
 * @package Component cedThumbnails for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/
defined('_JEXEC') or die();
jimport('joomla.application.component.view');

class CedThumbnailsViewFrontpage extends JView
{
    function display($tpl = null)
    {
        $this->defaultTpl($tpl);
    }

    function defaultTpl($tpl = null)
    {
        JToolBarHelper::title(JText::_('CedThumbnails'), 'tag.png');
        parent::display($tpl);
    }
}
