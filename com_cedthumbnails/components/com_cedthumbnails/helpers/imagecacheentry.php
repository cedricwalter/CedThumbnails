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

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class comCedThumbnailsImageCacheEntry
{
    var $resizeImageFilename = null;
    var $originalImagePath = null;

    public function __construct($imageModel, $desiredFileExtension = ".jpg")
    {
        $thumbnailFileName = pathinfo(trim($imageModel->fileName), PATHINFO_FILENAME) . $desiredFileExtension;

        $this->originalImagePath = $imageModel->path;

        $thumbnailFileName = str_replace("%20" , "-", $thumbnailFileName);

        $this->resizeImageFilename = JFile::makeSafe($thumbnailFileName);
    }

    public function getResizeImageFilename()
    {
        return $this->resizeImageFilename;
    }

    /**
     * @return null|string
     */
    public function getOriginalImagePath()
    {
        return $this->originalImagePath;
    }

}