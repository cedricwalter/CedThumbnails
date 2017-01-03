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

require_once(dirname(__FILE__) . '/filter.php');
require_once(dirname(__FILE__) . '/log.php');
require_once(dirname(__FILE__) . '/imagemodelfactory.php');

jimport('joomla.filesystem.folder');
jimport('joomla.image.image');
require_once(dirname(__FILE__) . '/log.php');
require_once(dirname(__FILE__) . '/imagecache.php');
require_once(dirname(__FILE__) . '/imagecacheentry.php');


/**
 * https://github.com/joomla/joomla-platform/blob/staging/docs/manual/en-US/chapters/packages/image.md
 */
class comCedThumbnailsImageResizer
{
    var $comCedThumbnailsImageCache = null;

    var $width = null;
    var $height = null;
    var $jpgQuality = null;
    var $pngQuality = null;
    var $scaleMethod = null;
    var $type = null;

    var $cachePath = null;

    public function __construct($width = 70, $height = 70,
                                $jpgQuality = 85, $pngQuality = 9,
                                $scaleMethod = JImage::SCALE_FILL,
                                $type = 2,
                                $comCedThumbnailsImageCache)
    {
        $this->width = $width;
        $this->height = $height;
        $this->jpgQuality = $jpgQuality;
        $this->pngQuality = $pngQuality;
        $this->scaleMethod = $scaleMethod;
        $this->type = $type;

        $this->comCedThumbnailsImageCache = $comCedThumbnailsImageCache;
    }

    public static function withParams($params, $comCedThumbnailsImageCache)
    {

        $scaleMethod = intval($params->get('scaleMethod', 1));
        $width = intval($params->get('thumbnailWidth', 70));
        $height = intval($params->get('thumbnailHeight', 70));
        $jpgQuality = $params->get('quality', 85);
        $pngQuality = $params->get('pngQuality', 9);
        $type = intval($params->get('thumbnailsOutput', 2));

        $instance = new self($width, $height,
            $jpgQuality, $pngQuality,
            $scaleMethod,
            $type,
            $comCedThumbnailsImageCache);

        return $instance;
    }


    public function resize($imageModel)
    {
        $cacheEntry = new comCedThumbnailsImageCacheEntry($imageModel, $this->getFileNameScaledExtension($this->type));

        // performance
        if ($this->comCedThumbnailsImageCache->entryExist($cacheEntry)) {
            return $cacheEntry->getResizeImageUrl();
        }

        try {
            //https://github.com/joomla/joomla-framework-image
            $jImage = new JImage($cacheEntry->getOriginalImagePath());

            $resizeJImage = $jImage->resize($this->width, $this->height, true, $this->scaleMethod);

            $resizeJImage->toFile(
                $this->comCedThumbnailsImageCache->getImagePath($cacheEntry), $this->getType(), $this->getOptions());

        } catch (Exception $e) {
            $message = 'Can not resize image url \'' . $this->toString($imageModel) . '\' to be written in \'' .
                $this->comCedThumbnailsImageCache->getImagePath($cacheEntry) . '\' cause \'' . $e->getMessage() . '\'';
            comCedThumbnailsLog::log($message, JLog::ERROR);

            return null;
        } //finally { only php 5.5
        unset($jImage);
        //}

        return $this->comCedThumbnailsImageCache->getImageUrl($cacheEntry);
    }

    private function toString($imageModel)
    {
        $path = "path:" . (property_exists($imageModel, "path") ? $imageModel->path : "");
        $string = "url: $imageModel->url $path filename: $imageModel->fileName";
    }

    private function getOptions()
    {
        $options = array();
        switch ($this->type) {
            case 1:
                break;
            case 2:
                $options = array('quality' => $this->jpgQuality);
                break;
            case 3:
                $options = array('quality' => $this->pngQuality);
                break;
        }

        return $options;
    }

    private function getType()
    {
        $extension = IMAGETYPE_JPEG;
        switch ($this->type) {
            case 1:
                $extension = IMAGETYPE_GIF;
                break;
            case 3:
                $extension = IMAGETYPE_PNG;
                break;
            case 2:
                $extension = IMAGETYPE_JPEG;
                break;
        }

        return $extension;
    }

    private function getFileNameScaledExtension($type = 2)
    {
        $extension = ".jpg";
        switch ($type) {
            case 1:
                $extension = ".gif";
                break;
            case 3:
                $extension = ".png";
                break;
            case 2:
                $extension = ".jpg";
                break;
        }

        return $extension;
    }


    function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
}
