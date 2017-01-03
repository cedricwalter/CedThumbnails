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
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/abstract.php');

require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';
require_once JPATH_SITE . '/components/com_cedthumbnails//helpers/imagedetector.php';
require_once JPATH_SITE . '/components/com_cedthumbnails//helpers/imageresizer.php';

class CedThumbnailsThumbnailsModel
{
	/**
	 * @param $model
	 * @param $params
	 * @param $items
	 *
	 * @return array
	 */
    public function getModel($model, $params, $items)
    {
        $comCedThumbnailsImageDetector = new comCedThumbnailsImageDetector();

        $defaultImageModel = comCedThumbnailsImageModelFactory::buildDefaultImageModel($params);
        $comCedThumbnailsFilter = new comCedThumbnailsFilter(JPATH_SITE, JURI::base(), $defaultImageModel);

        $comCedThumbnailsHelper = new comCedThumbnailsHelper();

        $showDateInDays = intval($params->get('showDateInDays', 1));
        $useThumbnails = intval($params->get('useThumbnails', 1));

        $commonParams = JComponentHelper::getParams("com_cedthumbnails");
        $aggressiveThumbnailsCaching = $commonParams->get('thumbnails-caching', 1);

        $cacheLocation = $params->get('cacheLocation', 'cache');

        $cache = new comCedThumbnailsImageCache(JUri::base(true).'/'.$cacheLocation, JPATH_SITE.DIRECTORY_SEPARATOR.$cacheLocation, "plg_cedthumbnails", $aggressiveThumbnailsCaching);

        $scale = comCedThumbnailsImageResizer::withParams($params, $cache);

        $entries = array();
        foreach ($items as $item) {
            $entry = new stdClass();

            $entry->dateAgo = $comCedThumbnailsHelper->getDateRepresentation($params, $item->created, $showDateInDays);


            $entry->link = $model->getArticleLink($item);

            $length = $params->get('titleLength', '60');
            $entry->title = $comCedThumbnailsHelper->getTitle($item->title, $params->get('useTitle', 1), $length);
            $entry->description = $comCedThumbnailsHelper->getDescription($params, $item->introtext, $item->fulltext);
            $entry->alt = $comCedThumbnailsHelper->getImageAlt($item->title, $item->title);
            $entry->caption = $comCedThumbnailsHelper->getImageCaption($item->title, $item->title);

            if ($useThumbnails) {
                $imageModel = $comCedThumbnailsImageDetector->getImage($params, $item);

                $entry->image = $imageModel->url;

                $filteredImageModel = $comCedThumbnailsFilter->filter($imageModel);

                $entry->imgSrc = $scale->resize($filteredImageModel);

                $entry->alt = $comCedThumbnailsHelper->getImageAlt($item->title, $imageModel->alt);
                $entry->caption = $comCedThumbnailsHelper->getImageCaption($item->title, $imageModel->caption);

                $entry->width = intval($params->get('thumbnailWidth', 70));
                $entry->height = intval($params->get('thumbnailHeight', 70));
            }

            $entries[] = $entry;
        }


        return $entries;
    }

}
