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

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
jimport('joomla.filesystem.folder');

class CacheEntry
{
	var $fileName = null;
	var $filePath = null;

	public function __construct($filePath, $fileName, $desiredFileExtension = ".jpg", $width, $height)
	{
		$this->filePath = $filePath;

		// append size to avoid resizing if user change thumbnails size back and forth
		$fileName = pathinfo(trim($fileName), PATHINFO_FILENAME) . "-$width"."x"."$height" . $desiredFileExtension;

		// Remove any trailing dots, as those aren't ever valid file names.
		$fileName = rtrim($fileName, '.');

		$fileName = str_replace("%20", "-", $fileName);
		$fileName = str_replace(" ", "-", $fileName);

		// windows linux invalid
		$fileName = str_replace("?", "", $fileName);
		$fileName = str_replace(",", "", $fileName);
		$fileName = str_replace("/", "", $fileName);
		$fileName = str_replace("\\", "", $fileName);
		$fileName = str_replace("<", "", $fileName);
		$fileName = str_replace(">", "", $fileName);
		$fileName = str_replace("*", "", $fileName);
		$fileName = str_replace("|", "", $fileName);

		$this->fileName = trim($fileName);
	}

	public function getFileName()
	{
		return $this->fileName;
	}

	public function getFilePath()
	{
		return $this->filePath;
	}

}