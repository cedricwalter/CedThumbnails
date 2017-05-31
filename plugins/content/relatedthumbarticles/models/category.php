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

require_once(dirname(__FILE__) . '/abstract.php');

class CedThumbnailsCategoryModel extends CedThumbnailsAbstractModel
{

    public function getModel($params, $articleId, $categoryId)
    {
        $database = JFactory::getDbo();
        $query = $this->getMainSql($params, $articleId, $categoryId);

        $query->order($this->getOrderBySql($params));

        $limit = intval($params->get('limit', 10));
        $database->setQuery($query, 0, $limit);

        //$dump = $query->dump();

        return $database->loadObjectList();
    }


}
