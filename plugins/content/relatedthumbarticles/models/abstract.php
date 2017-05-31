<?php
/**
 * @package     cedThumbnails
 * @subpackage  plg_content_relatedthumbarticles
 *
 * @copyright   CedThumbnails 3.1.3 - Copyright (C) 2013-2017 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

abstract class CedThumbnailsAbstractModel
{

    public function __construct()
    {
    }

    public function getModel($params, $articleId, $categoryId)
    {
    }

    protected function getOrderBySql($params)
    {
        $random = intval($params->get('random', 1));
        if ($random) {
            return 'rand()';
        } else {
            $orderBy = $params->get('orderby', 'created');
            $orderBy2 = $params->get('orderby2', 'DESC');
            return $orderBy . ' ' . $orderBy2;
        }
    }

    /**
     * @param $params
     * @param $articleId
     * @param $categoryId
     * @return JDatabaseQuery
     */
    protected function getMainSql($params, $articleId, $categoryId)
    {
        $filterFeatured = $params->get('filterFeatured', 'show');
        $filterLanguage = intval($params->get('filterLanguage', 1));

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select('a.id');
        $query->select('a.title');
        $query->select('a.created');
        $query->select('a.introtext');
        $query->select('a.fulltext');
        $query->select('a.images');

        $query->select('CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug');
        $query->select('CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as catslug');

        $query->from('#__content as a');
        $query->innerJoin('#__categories AS b ON b.id=a.catid');

        $query->where('a.checked_out =0');
        $query->where('b.published =1');

        // Filter publish date
        $nullDate = $db->quote($db->getNullDate());
        $query->where('(' . $query->currentTimestamp() . ' >= a.publish_up OR a.publish_up = ' . $nullDate . ')');
        $query->where('(' . $query->currentTimestamp() . ' <= a.publish_down OR a.publish_down = ' . $nullDate . ')');

        // Filter by access level.
        if ($access = $params->get('filterAccess')) {
            $user = JFactory::getUser();
            $groups = implode(',', $user->getAuthorisedViewLevels());
            $query->where('a.access IN (' . $groups . ')');
            $query->where('b.access IN (' . $groups . ')');
        }

        // Filter by published state
        $filterPublished = $params->get('filterPublished', 1);
        if ($filterPublished) {
            $query->where('a.state = 1');
        } else {
            $query->where('(a.state = 0 OR a.state = 1)');
        }

        // Filter by featured state
        switch ($filterFeatured) {
            case 'hide':
                $query->where('a.featured = 0');
                break;

            case 'only':
                $query->where('a.featured = 1');
                break;

            case 'show':
            default:
                // Normally we do not discriminate
                // between featured/unfeatured items.
                break;
        }

        // Filter by language
        if ($filterLanguage) {
            $query->where('a.language in (' . $db->quote(JFactory::getLanguage()->getTag()) . ',' . $db->quote('*') . ')');
        }

        if ($categoryId != null) {
            $query->where('a.catid =' . $db->quote(intval($categoryId)));
        }
        if ($articleId != null) {
            $query->where('a.id !=' . $db->quote(intval($articleId)));
        }

        return $query;
    }

	public function getArticleLink($row2)
	{
		$url = JRoute::_(ContentHelperRoute::getArticleRoute($row2->slug, $row2->catslug));

		$uri = JURI::getInstance();
		$prefix = $uri->toString(array('scheme', 'host', 'port'));
		$JConfig = new JConfig;
		return $JConfig->sef ? $prefix . JRoute::_($url) : $url;
	}


}
