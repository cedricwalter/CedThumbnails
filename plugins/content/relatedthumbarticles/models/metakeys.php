<?php
/**
 * @package     cedThumbnails
 * @subpackage  plg_content_relatedthumbarticles
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id          1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/abstract.php');

class CedThumbnailsMetakeysModel extends CedThumbnailsAbstractModel
{
	public function getModel($params, $articleId, $categoryId)
	{
		$query = $this->getMainSql($params, $articleId, $categoryId);

		$like = $this->getMetakeysFromArticle($articleId);
		if (count($like))
		{
			$like = "'%" . implode("%' OR a.metakey LIKE '%", $like) . "%'";
			$query->where('(a.metakey LIKE ' . $like . ')');
		}
		$query->order($this->getOrderBySql($params));

		$database = JFactory::getDbo();
		$limit    = intval($params->get('limit', 10));
		$database->setQuery($query, 0, $limit);

		return $database->loadObjectList();
	}

	/**
	 * @param $articleId
	 *
	 * @return array token of meta keys
	 */
	private function getMetakeysFromArticle($articleId)
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('metakey');
		$query->from('#__content');
		$query->where('id = ' . $db->quote(intval($articleId)));

		$db->setQuery($query);

		$metakeys = trim($db->loadResult());
		$like     = array();
		if ($metakey = $metakeys)
		{
			$keys = explode(',', $metakey);
			foreach ($keys as $key)
			{
				$key = trim($key);
				if ($key)
				{
					$like[] = $key;
				}
			}
		}

		return $like;
	}

}
