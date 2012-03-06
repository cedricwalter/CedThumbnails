<?php
/**
 * @version        CedThumbnails
 * @package
 * @copyright    Copyright (C) 2009 Cedric Walter. All rights reserved.
 * @copyright    www.cedricwalter.com / www.waltercedric.com
 *
 * @license        GNU/GPL, see LICENSE.php
 *
 * CedThumbnails is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once dirname(__FILE__) . '/helper.php';

$cacheParameters = new stdClass;
$cacheParameters->cachemode = 'safeuri';
$cacheParameters->class = 'modRelatedItemsThumbHelper';
$cacheParameters->method = 'getList';
$cacheParameters->methodparams = $params;
$cacheParameters->modeparams = array('id' => 'int', 'Itemid' => 'int');

$list = JModuleHelper::moduleCache($module, $params, $cacheParameters);

if (!count($list)) {
    return;
}

$layout = $params->get('layout', 'default');
//because of joomla fail module caching, add css in here and cache output of module myself
modRelatedItemsThumbHelper::addStyleSheet($layout);

$showDate = $params->get('showDate', 0);

require JModuleHelper::getLayoutPath('mod_related_items_thumb', $layout);
