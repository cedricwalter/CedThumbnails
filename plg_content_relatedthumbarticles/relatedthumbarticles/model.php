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
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

class relatedThumbArticlesModel extends JObject
{

    var $orderBy2 = null;
    var $orderBy = null;
    var $limit = null;
    var $random = null;
    var $textAfter = null;
    var $searchMode = null;
    var $articleId = null;
    var $categoryId = null;
    var $access = null;

    function relatedThumbArticlesModel($params)
    {
        $this->orderBy = intval($params->get('orderby', 0));
        $this->orderBy2 = intval($params->get('orderby2', 0));
        $this->random = intval($params->get('random', 0));
        $this->limit = intval($params->get('limit', 10));
        $this->textAfter = $params->get('textafter', '');
        $this->searchMode = intval($params->get('searchMode', 0));
    }

    public function getRelatedArticleRows($articleId, $categoryId, $access)
    {
        $this->articleId = $articleId;
        $this->categoryId = $categoryId;
        $this->access = $access;

        //use metakeys
        if ($this->searchMode == 0) {
            return $this->getRelatedItemsByMetakeys();
        }
        // same category
        return $this->getRelatedItemsByCategory();
    }

    private function getLimitSql()
    {
        return ' LIMIT ' . intval($this->limit);
    }

    private function getOrderBySql()
    {
        if ($this->random) {
            $orderBy = "rand()";
        }
        else {
            $orderBy = "id";
            if ($this->orderBy == 1) {
                $orderBy = "created";
            } elseif ($this->orderBy == 2) {
                $orderBy = "title";
            }

            if ($this->orderBy2 == 0) {
                $orderBy .= " DESC";
            } else {
                $orderBy .= " ASC";
            }
        }
        return ' ORDER BY ' . $orderBy;
    }

    private function getMainSql()
    {
        $user =& JFactory::getUser();
        $JConfig = new JConfig;
        $nowDate = date('Y-m-d H:i:s', time() + $JConfig->offset * 60 * 60);

        if (comCedThumbnailsHelper::isJoomla15()) {
            $query = 'SELECT a.id, a.title, a.created, a.introtext, a.fulltext, '
                             . ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
                             . ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as catslug,'
                             . ' u.id AS sectionid'
                             . ' FROM #__content as a'
                             . ' INNER JOIN #__categories AS b ON b.id=a.catid'
                             . ' INNER JOIN #__sections AS u ON u.id = a.sectionid'
                             . ' WHERE ( state = \'1\' AND a.checked_out = 0 )'
                             . ' AND a.state = 1'
                             . ' AND u.published = 1'
                             . ' AND b.published = 1'
                             . ' AND ( publish_up = \'0000-00-00 00:00:00\' OR publish_up <= \'' . $nowDate . '\' )'
                             . ' AND ( publish_down = \'0000-00-00 00:00:00\' OR publish_down >= \'' . $nowDate . '\' )'
                             . ($this->access ? '\n AND access <= \'' . $user->gid . '\'' : '');
        } else {
            $query = 'SELECT a.id, a.title, a.created, a.introtext, a.fulltext, '
                     . ' CASE WHEN CHAR_LENGTH(a.alias) THEN CONCAT_WS(":", a.id, a.alias) ELSE a.id END as slug,'
                     . ' CASE WHEN CHAR_LENGTH(b.alias) THEN CONCAT_WS(":", b.id, b.alias) ELSE b.id END as catslug';
            $query .= ' FROM #__content as a'
                      . ' INNER JOIN #__categories AS b ON b.id=a.catid';
            $query .= ' WHERE ( state = \'1\' AND a.checked_out = 0 )'
                      . ' AND a.state = 1';
            $query .= ' AND b.published = 1'
                      . ' AND ( publish_up = \'0000-00-00 00:00:00\' OR publish_up <= \'' . $nowDate . '\' )'
                      . ' AND ( publish_down = \'0000-00-00 00:00:00\' OR publish_down >= \'' . $nowDate . '\' )'
                      . ($this->access ? '\n AND access <= \'' . $user->gid . '\'' : '');
        }

        if ($this->categoryId != null) {
            $query .= ' AND a.catid = ' . intval($this->categoryId);
        }
        if ($this->articleId != null) {
            $query .= ' AND a.id != ' . intval($this->articleId);
        }

        return $query;
    }

    private function getRelatedItemsByCategory()
    {
        $database = & JFactory::getDBO();
        $query = $this->getMainSql()
                 . $this->getOrderBySql()
                 . $this->getLimitSql();
        //error_log($query);
        $database->setQuery($query);
        return $database->loadObjectList();
    }


    private function getMetakeysFromArticle()
    {
        $database = & JFactory::getDBO();
        $query = 'SELECT metakey FROM #__content WHERE id=' . intval($this->articleId);
        $database->setQuery($query);
        $metakeys = trim($database->loadResult());
        $like = array();
        if ($metakey = $metakeys) {
            $keys = explode(',', $metakey);
            foreach ($keys as $key) {
                $key = trim($key);
                if ($key) {
                    $like[] = $key;
                }
            }
        }
        return $like;
    }


    private function getRelatedItemsByMetakeys()
    {
        $database = & JFactory::getDBO();
        $query = $this->getMainSql();

        $like = $this->getMetakeysFromArticle();
        if (count($like)) {
            $like = "'%" . implode("%' OR a.metakey LIKE '%", $like) . "%'";
            $query .= " AND (a.metakey LIKE " .$like .")";
        }
        $query .= $this->getOrderBySql() . $this->getLimitSql();
        $database->setQuery($query);
        return $database->loadObjectList();
    }

}