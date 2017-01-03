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

class wwwRemoval extends Filter
{

    private $successor;

    public function setSuccessor($nextFilter)
    {
        $this->successor = $nextFilter;
    }

    public function handleRequest(&$unknownImagePath)
    {
        $hostWithoutWWW = str_replace("www.", "", $this->hostname);
        if ($this->startsWith($unknownImagePath, $hostWithoutWWW)) {
            $unknownImagePath = str_replace($hostWithoutWWW, "", $unknownImagePath);
        }

        comCedThumbnailsLog::log("wwwRemoval::handleRequest " . $unknownImagePath);

        if (isset($this->successor))
        {
            $this->successor->handleRequest ($unknownImagePath);
        }

        return $unknownImagePath;
    }

}