<?php
/**
 * @package     cedThumbnails
 * @subpackage  com_cedthumbnails
 *
 * @copyright   CedThumbnails 3.1.3 - Copyright (C) 2013-2017 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// no direct access
use cedthumbnails\Cache;
use cedthumbnails\ImageDetector;
use cedthumbnails\ImageFactory;
use cedthumbnails\ImageResizer;

defined('_JEXEC') or die('Restricted access');

require_once dirname(__FILE__) . '/helper.php';
require_once dirname(__FILE__) . '/imagedetector.php';
require_once dirname(__FILE__) . '/imageresizer.php';
require_once dirname(__FILE__) . '/cache.php';

class cedThumbnailsDecorator
{

	private $imageDetector;
	private $helper;
	private $resizer;
	private $modelFactory;
	private $defaultImageModel;

	public function __construct($params)
	{
		$this->helper        = new comCedThumbnailsHelper();

		$this->modelFactory  = new ImageFactory();

		$this->defaultImageModel = $this->modelFactory->buildDefaultImageModel($params);

		$this->imageDetector = new ImageDetector(intval($params->get('originThumbnails', 1)), $this->defaultImageModel);
	}

	public function decorate(&$params, &$items, $extension)
	{
		$componentParams             = JComponentHelper::getParams("com_cedthumbnails");
		$aggressiveThumbnailsCaching = $componentParams->get('thumbnails-caching', 1);

		$cacheLocation = $params->get('cacheLocation', "cache");

		// true use relative url, need / with false dont need it
		$cache = new Cache(JUri::base(true) . '/' . $cacheLocation, JPATH_SITE . DIRECTORY_SEPARATOR . $cacheLocation, $extension, $aggressiveThumbnailsCaching);

		$scale = ImageResizer::withParams($params, $cache);

		$useThumbnails = $params->get('useThumbnails', 1);

		$defaultImageModel = $this->modelFactory->buildDefaultImageModel($params);

		$i = 0;
		foreach ($items as &$item)
		{
			$item->teaser = $this->helper->getDescription($params, $item->introtext, $item->fulltext);
			$item->title  = $this->helper->getTitle($item->title, $params->get('useTitle', 1), $params->get('titleLength', '60'));

			if ($useThumbnails)
			{
				$imageModel = $this->imageDetector->getImage($item);

				$item->image = $imageModel->url;

				$resizedImage = $scale->resize($imageModel);
				if ($resizedImage == null)
				{
					// exception while resizing
					$item->resizedImage = $defaultImageModel->url;
				}
				else
				{
					$item->resizedImage = $resizedImage;
				}

				$item->alt     = $this->helper->getImageAlt($item->title, $imageModel->alt);
				$item->caption = $this->helper->getImageCaption($item->title, $imageModel->caption);
			}

			$i++;
		}

		return $items;
	}
}