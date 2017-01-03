<?php
/**
 * @package     cedThumbnails
 * @subpackage  mod_related_items_thumb
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// no direct access
defined('_JEXEC') or die;
require_once dirname(__FILE__) . '/helper.php';
require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';

$idbase = $params->get('catid');
$cacheid = md5(serialize(array ($idbase, $module->module)));

$cachePathId = JPath::clean(trim(str_replace(" ", "", $module->title)));

$cacheparams = new stdClass;
$cacheparams->cachemode = 'safeuri';
$cacheparams->class = 'modRelatedItemsThumbHelper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = array(&$params, $cachePathId);
$cacheparams->modeparams = array('id' => 'int', 'Itemid' => 'int');

$list = JModuleHelper::moduleCache($module, $params, $cacheparams);

if (!count($list)) {
    return;
}

modRelatedItemsThumbHelper::addStyleSheet($params->get('layout', 'default'));

$showDate = $params->get('showDate', 0);
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'));

require JModuleHelper::getLayoutPath('mod_related_items_thumb', $params->get('layout', 'default'));
