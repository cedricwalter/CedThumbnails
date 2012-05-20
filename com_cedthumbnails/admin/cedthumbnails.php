<?php
/**
 * @package Component cedThumbnails for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/
defined('_JEXEC') or die('Restricted access');

// Include dependencies
jimport('joomla.application.component.controller');
require_once (JPATH_COMPONENT . '/controller.php');


$document = & JFactory::getDocument();
$document->addStyleSheet(JURI::root() . '/media/com_cedtag/css/admintag.css');

$jinput = JFactory::getApplication()->input;

$controller = JFactory::getApplication()->input->get('controller');
$task = JFactory::getApplication()->input->get('task');

// Create the controller
$classname = 'CedThumbnailsController' . $controller;

$controller = new $classname();

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

?>
