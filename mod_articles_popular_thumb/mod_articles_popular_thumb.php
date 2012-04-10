<?php
/**
 * @copyright    Copyright (C) 2011 Cedric Walter from www.waltercedric.com. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 *
 * mod_articles_popular_thumb is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.

 * Author: Cedric Walter
 * Email: cedric.walter@gmail.com
 * Web: http://www.waltercedric.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__).'/helper.php';

$layout = $params->get('layout', 'default');
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

modMostReadThumbHelper::addStyleSheet($layout);
$list = modMostReadThumbHelper::getList($params);

require JModuleHelper::getLayoutPath('mod_articles_popular_thumb', $layout);