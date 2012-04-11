<?php
/**
 * @version        $Id: mod_articles_latest.php 20806 2011-02-21 19:44:59Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    mod_articles_latest
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/helper.php';

$layout = $params->get('layout', 'default');
modArticlesLatestHelper::addStyleSheet($layout);

$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));
$list = modArticlesLatestHelper::getList($params);
require JModuleHelper::getLayoutPath('mod_articles_latest_thumb', $layout);