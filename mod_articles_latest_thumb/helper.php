<?php
/**
 * @version        $Id: helper.php 21451 2011-06-04 19:00:00Z dextercowley $
 * @package        Joomla.Site
 * @subpackage    mod_articles_latest
 * @copyright    Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license        GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedthumbnails/helper.php';

jimport('joomla.application.component.model');

JModel::addIncludePath(JPATH_SITE . '/components/com_content/models', 'ContentModel');

abstract class modArticlesLatestHelper
{
    public static function getList(&$params)
    {
        // Get the dbo
        $db = JFactory::getDbo();

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

        // User filter
        $userId = JFactory::getUser()->get('id');
        switch ($params->get('user_id'))
        {
            case 'by_me':
                $model->setState('filter.author_id', (int)$userId);
                break;
            case 'not_me':
                $model->setState('filter.author_id', $userId);
                $model->setState('filter.author_id.include', false);
                break;

            case '0':
                break;

            default:
                $model->setState('filter.author_id', (int)$params->get('user_id'));
                break;
        }

        // Filter by language
        $model->setState('filter.language', $app->getLanguageFilter());

        //  Featured switch
        switch ($params->get('show_featured'))
        {
            case '1':
                $model->setState('filter.featured', 'only');
                break;
            case '0':
                $model->setState('filter.featured', 'hide');
                break;
            default:
                $model->setState('filter.featured', 'show');
                break;
        }

        // Set ordering
        $order_map = array(
            'm_dsc' => 'a.modified DESC, a.created',
            'mc_dsc' => 'CASE WHEN (a.modified = ' . $db->quote($db->getNullDate()) . ') THEN a.created ELSE a.modified END',
            'c_dsc' => 'a.created',
            'p_dsc' => 'a.publish_up',
        );
        $ordering = JArrayHelper::getValue($order_map, $params->get('ordering'), 'a.publish_up');
        $dir = 'DESC';

        $model->setState('list.ordering', $ordering);
        $model->setState('list.direction', $dir);

        $items = $model->getItems();

        $i = 0;
        $lists = array();
        foreach ($items as &$item) {
            $item->slug = $item->id . ':' . $item->alias;
            $item->catslug = $item->catid . ':' . $item->category_alias;

            if ($access || in_array($item->access, $authorised)) {
                // We know that user has the privilege to view the article
                $item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
            } else {
                $item->link = JRoute::_('index.php?option=com_users&view=login');
            }

            //decorate the original model $item
            $lists[$i]->item = $item;
            $lists[$i]->image = comCedThumbnailsHelper::getImage($params, $item);
            $lists[$i]->imageSrc = comCedThumbnailsHelper::getResizedImageSource($params, $lists[$i]->image, "mod_articles_latest_thumb");
            $lists[$i]->teaser = comCedThumbnailsHelper::getDescription($params, $item->introtext);
            $lists[$i]->title =  comCedThumbnailsHelper::getTitle($params, $item->title);
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
        $document->addStyleSheet("media/mod_articles_latest_thumb/".substr($layout, 2).".css");
    }
}
