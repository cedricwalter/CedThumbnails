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
 */

defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_content/helpers/route.php';

abstract class modRelatedItemsThumbHelper
{
    /**
     * Get a list of related articles
     *
     * @param   \Joomla\Registry\Registry  &$params  module parameters
     *
     * @return array
     */
    public static function getList(&$params, $cachePathId)
    {
        $db = JFactory::getDbo();
        $app = JFactory::getApplication();
        $user = JFactory::getUser();
        $groups = implode(',', $user->getAuthorisedViewLevels());
        $date = JFactory::getDate();
        $maximum = (int) $params->get('maximum', 5);

        $option = $app->input->get('option');
        $view = $app->input->get('view');

        $temp = $app->input->getString('id');
        $temp = explode(':', $temp);
        $id = $temp[0];

        $nullDate = $db->getNullDate();
        $now = $date->toSql();
        $related = array();
        $query = $db->getQuery(true);

        if ($option == 'com_content' && $view == 'article' && $id)
        {
            // Select the meta keywords from the item
            $query->select('metakey')
                ->from('#__content')
                ->where('id = ' . (int) $id);
            $db->setQuery($query);

            if ($metakey = trim($db->loadResult()))
            {
                // Explode the meta keys on a comma
                $keys = explode(',', $metakey);
                $likes = array();

                // Assemble any non-blank word(s)
                foreach ($keys as $key)
                {
                    $key = trim($key);

                    if ($key)
                    {
                        $likes[] = $db->escape($key);
                    }
                }

                if (count($likes))
                {
                    // Select other items based on the metakey field 'like' the keys found
                    $query->clear()
                        ->select('a.id')
                        ->select('a.title')
                        ->select('a.introtext')
                        ->select('a.fulltext')
                        ->select('a.images')
                        ->select('DATE(a.created) as created')
                        ->select('a.catid')
                        ->select('a.language')
                        ->select('cc.access AS cat_access')
                        ->select('cc.published AS cat_state');

                    // Sqlsrv changes
                    $case_when = ' CASE WHEN ';
                    $case_when .= $query->charLength('a.alias', '!=', '0');
                    $case_when .= ' THEN ';
                    $a_id = $query->castAsChar('a.id');
                    $case_when .= $query->concatenate(array($a_id, 'a.alias'), ':');
                    $case_when .= ' ELSE ';
                    $case_when .= $a_id . ' END as slug';
                    $query->select($case_when);

                    $case_when = ' CASE WHEN ';
                    $case_when .= $query->charLength('cc.alias', '!=', '0');
                    $case_when .= ' THEN ';
                    $c_id = $query->castAsChar('cc.id');
                    $case_when .= $query->concatenate(array($c_id, 'cc.alias'), ':');
                    $case_when .= ' ELSE ';
                    $case_when .= $c_id . ' END as catslug';
                    $query->select($case_when)
                        ->from('#__content AS a')
                        ->join('LEFT', '#__content_frontpage AS f ON f.content_id = a.id')
                        ->join('LEFT', '#__categories AS cc ON cc.id = a.catid')
                        ->where('a.id != ' . (int) $id)
                        ->where('a.state = 1')
                        ->where('a.access IN (' . $groups . ')');

                    $wheres = array();

                    foreach ($likes as $keyword)
                    {
                        $wheres[] = 'a.metakey LIKE ' . $db->quote('%' . $keyword . '%');
                    }

                    $query->where('(' . implode(' OR ', $wheres) . ')')
                        ->where('(a.publish_up = ' . $db->quote($nullDate) . ' OR a.publish_up <= ' . $db->quote($now) . ')')
                        ->where('(a.publish_down = ' . $db->quote($nullDate) . ' OR a.publish_down >= ' . $db->quote($now) . ')');

                    // Filter by language
                    if (JLanguageMultilang::isEnabled())
                    {
                        $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
                    }

                    $db->setQuery($query, 0, $maximum);
                    $temp = $db->loadObjectList();

                    if (count($temp))
                    {
                        foreach ($temp as $row)
                        {
                            if ($row->cat_state == 1)
                            {
                                $row->route = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catid, $row->language));
                                $related[] = $row;
                            }
                        }
                    }

                    unset ($temp);
                }
            }
        }

        require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/decorator.php';
        return cedThumbnailsDecorator::decorate($params, $related, "Mod_CedThumbnailsRelated-".$cachePathId);
   	}

    /**
     * Add stylesheet to document <head>
     */
    public static function addStyleSheet($layout)
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::base().'/media/mod_related_items_thumb/' . substr($layout, 2) . ".css?v=2.9.2");
    }
}
