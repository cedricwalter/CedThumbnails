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

jimport('joomla.plugin.plugin');

require_once(dirname(__FILE__) . '/controller.php');
require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';

/**
 * TODO use http://www.appelsiini.net/projects/lazyload
 *
 * Plugin caching is already done in Joomla core
 */
class plgContentRelatedThumbArticles extends JPlugin
{

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        //Do not run in admin area and non HTML  (rss, json, error)
        $app = JFactory::getApplication();
        if ($app->isAdmin() || JFactory::getDocument()->getType() !== 'html')
        {
            return true;
        }

	    $view = JFactory::getApplication()->input->get('view');

	    $isContent  = $context == 'com_content.article' && $view == 'article';
	    $isK2  = $context == 'com_k2.item' && $view == 'item';

	    $canProceed = $isContent || $isK2;
        if (!$canProceed) {
            return;
        }

        $print = JFactory::getApplication()->input->get('print') == 1;
        if (array_key_exists('catid', $row) && !$print) {
            $comCedThumbnailsHelper = new comCedThumbnailsHelper();

	        $menu_items_mode = intval($params->get('menu_items_mode'));
            $isExcluded = $comCedThumbnailsHelper->isExcluded($this->params);
			if ($menu_items_mode == 1 && !$isExcluded) {
				return;
			}
	        if ($menu_items_mode === 2 && $isExcluded) {
		        return;
	        }

	        if ($comCedThumbnailsHelper->isActiveInCategory($this->params, $row->catid) == false) {
                return;
            }

            $controller = relatedThumbArticlesController::getInstance($this->params);
            $html = $controller->execute($this->params, $row->id, $row->catid);

            if (!is_null($html)) {
                $controller->addResources();
                $position = intval($this->params->get('position', 1));
                if ($position) {
                    $row->text = $row->text . $html;
                } else {
                    $row->text = $html . $row->text;
                }
            }
        }

        return;
    }
}