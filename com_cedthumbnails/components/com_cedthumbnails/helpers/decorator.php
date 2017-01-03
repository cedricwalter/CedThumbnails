<?php
/**
 * @package     cedThumbnails
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

require_once dirname(__FILE__). '/helper.php';
require_once dirname(__FILE__). '/imagedetector.php';
require_once dirname(__FILE__). '/imageresizer.php';

class cedThumbnailsDecorator
{

    public static function decorate(&$params, &$items, $extension)
    {
        $imageDetector = new comCedThumbnailsImageDetector();
        $helper = new comCedThumbnailsHelper();


        $componentParams = JComponentHelper::getParams("com_cedthumbnails");
        $aggressiveThumbnailsCaching = $componentParams->get('thumbnails-caching', 1);

        $cacheLocation = $params->get('cacheLocation', "cache");

	    // true use relative url, need / with false dont need it
        $cache = new comCedThumbnailsImageCache(JUri::base(true).'/'.$cacheLocation, JPATH_SITE.DIRECTORY_SEPARATOR.$cacheLocation, $extension, $aggressiveThumbnailsCaching);

        $scale = comCedThumbnailsImageResizer::withParams($params, $cache);

        $useThumbnails = $params->get('useThumbnails', 1);
        $defaultImageModel = comCedThumbnailsImageModelFactory::buildDefaultImageModel($params);

        $comCedThumbnailsFilter = new comCedThumbnailsFilter(JPATH_SITE, JURI::base(), $defaultImageModel);

        $i = 0;
        foreach ($items as &$item) {
            $item->teaser = $helper->getDescription($params, $item->introtext, $item->fulltext);
            $item->title = $helper->getTitle($item->title, $params->get('useTitle', 1), $params->get('titleLength', '60'));

            if ($useThumbnails) {
                $imageModel = $imageDetector->getImage($params, $item);

                $item->image = $imageModel->url;

                $filteredImageModel = $comCedThumbnailsFilter->filter($imageModel);

                $resize = $scale->resize($filteredImageModel);
                if ($resize == null) {
                    // exception while resizing
                    $item->imgSrc = $defaultImageModel->url;
                } else {
                    $item->imgSrc = $resize;
                }

                $item->alt = $helper->getImageAlt($item->title, $imageModel->alt);
                $item->caption = $helper->getImageCaption($item->title, $imageModel->caption);
            }

            $i++;
        }

        return $items;
    }
}