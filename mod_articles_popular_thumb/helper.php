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

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

class modMostReadThumbHelper
{


    function getList(&$params)
    {
        // Get an instance of the generic articles model
        $model = JModel::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

        // Set application parameters in model
        $app = JFactory::getApplication();
        $appParams = $app->getParams();
        $model->setState('params', $appParams);

        // Set the filters based on the module params
        $model->setState('list.start', 0);
        $model->setState('list.limit', (int)$params->get('count', 5));

        $model->setState('list.select', 'a.fulltext, a.id, a.title, a.alias, a.title_alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,' .
        			' a.modified, a.modified_by, a.publish_up, a.publish_down, a.images, a.urls, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' .
        			' a.hits, a.featured');

        $model->setState('filter.published', 1);

        // Access filter
        $access = !JComponentHelper::getParams('com_content')->get('show_noauth');
        $authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
        $model->setState('filter.access', $access);

        // Category filter
        $model->setState('filter.category_id', $params->get('catid', array()));

        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());

        // Ordering
        $model->setState('list.ordering', 'a.hits');
        $model->setState('list.direction', 'DESC');

        $items = $model->getItems();

        $comCedThumbnailsHelper =  new comCedThumbnailsHelper();

        $i = 0;
        $lists = array();
        foreach ($items as &$item) {
            $item->slug = $item->id . ':' . $item->alias;
            $item->catslug = $item->catid . ':' . $item->category_alias;

            if ($access || in_array($item->access, $authorised)) {
                // We know that user has the privilege to view the article
                $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
            } else {
                $item->link = JRoute::_('index.php?option=com_user&view=login');
            }
            $lists[$i]->item = $item;
            $lists[$i]->image = $comCedThumbnailsHelper->getImage($params, $item);
            $lists[$i]->imageSrc = $comCedThumbnailsHelper->getResizedImageSource($params, $lists[$i]->image, "mod_articles_popular_thumb");
            $lists[$i]->title = $comCedThumbnailsHelper->getTitle($params, $item->title);
            $lists[$i]->teaser = $comCedThumbnailsHelper->getDescription($params, $item->introtext);
            $lists[$i]->text = htmlspecialchars($item->title);
            $i++;
        }

        return $lists;
    }

    /**
     * Add stylesheet to document <head>
     */
    public function addStyleSheet($layout)
    {
        $document =& JFactory::getDocument();
        $document->addStyleSheet("media/mod_articles_popular_thumb/" . substr($layout, 2) . ".css");
    }

}
