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
require_once dirname(__FILE__) . '/helper.php';
$layout = $params->get('layout', 'default');
modArticlesLatestHelper::addStyleSheet($layout);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list = modArticlesLatestHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_articles_latest_thumb', $layout);