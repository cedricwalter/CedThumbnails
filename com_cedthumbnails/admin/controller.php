<?php
/**
 * @package Component cedThumbnails for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/
jimport('joomla.application.component.controller');
jimport('joomla.application.input');

/**
 * Joomla Tag component Controller
 *
 */
class CedThumbnailsController extends JController
{
    protected $default_view = 'frontpage';

    function display()
    {
        $view = JFactory::getApplication()->input->get('view');
        if (!isset($view)) {
            JFactory::getApplication()->input->set('view', 'frontpage');
        }
        parent::display();
    }

}

?>
