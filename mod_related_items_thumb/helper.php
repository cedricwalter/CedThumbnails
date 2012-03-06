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

require_once JPATH_SITE . '/components/com_content/helpers/route.php';
require_once JPATH_SITE . '/components/com_cedthumbnails/helper.php';

abstract class modRelatedItemsThumbHelper
{
    public static function getList($params)
    {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $userId = (int)$user->get('id');
        $count = intval($params->get('count', 5));
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $date = JFactory::getDate();

        $option = JRequest::getCmd('option');
        $view = JRequest::getCmd('view');

        $temp = JRequest::getString('id');
        $temp = explode(':', $temp);
        $id = $temp[0];


        $showDate = $params->get('showDate', 0);
        $nullDate = $db->getNullDate();
        $now = $date->toMySQL();
        $related = array();
        $query = $db->getQuery(true);

        if ($option == 'com_content' && $view == 'article' && $id) {
            // select the meta keywords from the item

            $query->select('metakey');
            $query->from('#__content');
            $query->where('id = ' . (int)$id);
            $db->setQuery($query);

            if ($metakey = trim($db->loadResult())) {
                // explode the meta keys on a comma
                $keys = explode(',', $metakey);
                $likes = array();

                // assemble any non-blank word(s)
                foreach ($keys as $key)
                {
                    $key = trim($key);
                    if ($key) {
                        $likes[] = ',' . $db->getEscaped($key) . ','; // surround with commas so first and last items have surrounding commas
                    }
                }

                if (count($likes)) {
                    // select other items based on the metakey field 'like' the keys found
                    $query->clear();
                    $query->select('a.id');
                    $query->select('a.title');
                    $query->select('a.introtext');
                    $query->select('DATE_FORMAT(a.created, "%Y-%m-%d") as created');
                    $query->select('a.catid');
                    $query->select('cc.access AS cat_access');
                    $query->select('cc.published AS cat_state');
                    $query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
                    $query->select('CASE WHEN CHAR_LENGTH(cc.alias) THEN CONCAT_WS(":", cc.id, cc.alias) ELSE cc.id END as catslug');
                    $query->from('#__content AS a');
                    $query->leftJoin('#__content_frontpage AS f ON f.content_id = a.id');
                    $query->leftJoin('#__categories AS cc ON cc.id = a.catid');
                    $query->where('a.id != ' . (int)$id);
                    $query->where('a.state = 1');
                    $query->where('a.access IN (' . $groups . ')');
                    $query->where('(CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%' . implode('%" OR CONCAT(",", REPLACE(a.metakey, ", ", ","), ",") LIKE "%', $likes) . '%")'); //remove single space after commas in keywords)
                    $query->where('(a.publish_up = ' . $db->Quote($nullDate) . ' OR a.publish_up <= ' . $db->Quote($now) . ')');
                    $query->where('(a.publish_down = ' . $db->Quote($nullDate) . ' OR a.publish_down >= ' . $db->Quote($now) . ')');

                    // Filter by language
                    if ($app->getLanguageFilter()) {
                        $query->where('a.language in (' . $db->Quote(JFactory::getLanguage()->getTag()) . ',' . $db->Quote('*') . ')');
                    }

                    $db->setQuery($query);
                    $temp = $db->loadObjectList();

                    if (count($temp)) {

                        $i = 0;
                        foreach ($temp as $row)
                        {
                            if ($row->cat_state == 1) {
                                $row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));

                                $row->image = comCedThumbnailsHelper::getImage($params, $row);
                                $row->imageSrc = comCedThumbnailsHelper::getResizedImageSource($params, $row->image, "mod_related_items_thumb");
                                $row->title = comCedThumbnailsHelper::getTitle($params, $row->title);
                                $row->teaser = comCedThumbnailsHelper::getDescription($params, $row->introtext);
                                $row->text = htmlspecialchars($row->title);
                                $related[] = $row;

                                $i++;
                                if ($i == $count) {
                                    break;
                                }
                            }
                        }
                    }
                    unset ($temp);
                }
            }
        }

        return $related;
    }

    /**
     * Add stylesheet to document <head>
     */
    public function addStyleSheet($layout)
    {
        $document =& JFactory::getDocument();
        $document->addStyleSheet("media/mod_related_items_thumb/" . substr($layout, 2) . ".css");
    }
}
