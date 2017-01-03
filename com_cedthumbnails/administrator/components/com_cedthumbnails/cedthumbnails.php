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

require_once JPATH_COMPONENT_ADMINISTRATOR.'/liveupdate/liveupdate.php';
if( JFactory::getApplication()->input->get('view','') == 'liveupdate') {
    JToolBarHelper::preferences( 'com_cedthumbnails' );
    LiveUpdate::handleRequest();
    return;
}

jimport('joomla.application.component.controller');
require_once (JPATH_COMPONENT . '/controller.php');

$document = JFactory::getDocument();
$document->addStyleSheet(JURI::root() . 'media/com_cedthumbnails/css/thumbnails.css');

$controller = JFactory::getApplication()->input->get('controller');
$task = JFactory::getApplication()->input->get('task');

// Create the controller
$className = 'CedThumbnailsController' . $controller;

$controller = new $className();

// Perform the Request task
$controller->execute($task);

// Redirect if set by the controller
$controller->redirect();

