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

namespace cedthumbnails;

use JFile;
use JFolder;

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');
require_once(dirname(__FILE__) . '/log.php');

class Cache
{

    const URL_SEPARATOR = "/";

    var $aggressiveThumbnailsCaching = true;
    var $cacheFilePath = null;
    var $liveSiteCacheUrl = null;
    var $cacheGroup = null;

    function __construct($liveSiteCacheUrl, $cacheFilePath, $extensionCacheGroup, $aggressiveThumbnailsCaching)
    {
        $this->aggressiveThumbnailsCaching = $aggressiveThumbnailsCaching;
        $this->cacheFilePath = $cacheFilePath;
        $this->liveSiteCacheUrl = $liveSiteCacheUrl;
        $this->cacheGroup = (strlen($extensionCacheGroup) == 0 ? "default" : $extensionCacheGroup);
    }

    public function clear()
    {
        array_map('unlink', glob($this->cacheFilePath . '/*.*'));
    }

    /**
     * @param $cacheEntry
     * @return bool
     */
    public function entryExist($cacheEntry)
    {
	    $cacheGroupPath = $this->initCacheEntryPath();

        // performance
        return JFile::exists($cacheGroupPath . $cacheEntry->getFilename()) && $this->aggressiveThumbnailsCaching;
    }

    public function createEntry($cacheEntry)
    {
	    $cacheGroupPath = $this->initCacheEntryPath();

        $result = JFile::write($cacheGroupPath . $cacheEntry->getFilename(), true);
        if (!$result) {
        	error_log("can not write file in cache");
        }
    }

    public function getResourceEntry($cacheEntry)
    {
	    $cacheGroupPath = $this->initCacheEntryPath();

        return $cacheGroupPath . $cacheEntry->getFilename();
    }


    public function getImageUrl($cacheEntry)
    {
        $imageUrl = $this->addSeparatorIfNeeded($this->liveSiteCacheUrl, self::URL_SEPARATOR)
            . $this->cacheGroup . self::URL_SEPARATOR . $cacheEntry->getFilename();

        return $imageUrl;
    }

    public function getImagePath($cacheEntry)
    {
        return $this->addSeparatorIfNeeded($this->cacheFilePath, DIRECTORY_SEPARATOR)
        . $this->cacheGroup . DIRECTORY_SEPARATOR . $cacheEntry->getFilename();
    }

    private function endWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    /**
     * @param $path
     * @param $needle string to add if missing at the end
     * @return string
     */
    private function addSeparatorIfNeeded($path, $needle)
    {
        if (!self::endWith($path, $needle)) {
            $path = $path . $needle;
            return $path;
        }
        return $path;
    }

    /**
     * @param $extensionCache
     */
    private function createDirectoryIfNotExist($extensionCache)
    {
    	if (!JFolder::exists($extensionCache)) {
            JFolder::create($extensionCache);
        }
    }

	/**
	 * @return string
	 */
	private function initCacheEntryPath()
	{
		$this->createDirectoryIfNotExist($this->cacheFilePath);

		$cacheGroupPath = $this->cacheFilePath . DIRECTORY_SEPARATOR . $this->cacheGroup;
		$this->createDirectoryIfNotExist($cacheGroupPath);

		return $cacheGroupPath;
	}

}