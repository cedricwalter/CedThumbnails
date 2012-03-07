<?php
/**
 * @version        $Id: mod_related_items_thumb.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    mod_related_items_thumb
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
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
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_related_items_thumb', $layout);
