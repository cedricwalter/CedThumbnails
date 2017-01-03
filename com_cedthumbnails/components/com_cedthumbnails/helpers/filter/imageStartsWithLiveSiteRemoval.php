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

class imageStartsWithLiveSiteRemoval extends Filter
{

    private $successor;

    public function setSuccessor($nextFilter)
    {
        $this->successor = $nextFilter;

        return $this;
    }

    public function handleRequest(&$unknownImagePath)
    {
        $unknownImagePath = $this->imageStartsWithLiveSiteThenRemovedIt($unknownImagePath);
        $unknownImagePath = parse_url($unknownImagePath, PHP_URL_PATH);

        comCedThumbnailsLog::log("imageStartsWithLiveSiteRemoval " . $unknownImagePath);

        if (isset($this->successor))
        {
            $this->successor->handleRequest ($unknownImagePath);
        }

        return $unknownImagePath;
    }

    /**
     * @param $unknownImagePath
     * @return url
     * @internal param $liveSiteUrl
     * @internal param $imageModel
     */
    public function imageStartsWithLiveSiteThenRemovedIt($unknownImagePath)
    {
        $startWith1 = str_replace("http://", "https://", $this->liveSiteUrl);
        $startWith2 = str_replace("https://", "http://", $this->liveSiteUrl);

        if ($this->startsWith($unknownImagePath, $startWith1)) {
            return $this->removeLiveSiteUrl($startWith1, $unknownImagePath);
        } else
            if ($this->startsWith($unknownImagePath, $startWith2)) {
                return $this->removeLiveSiteUrl($startWith2, $unknownImagePath);
            }

        return $unknownImagePath;
    }



    protected function removeLiveSiteUrl($replace, $unknownImagePath)
    {
        return str_replace($replace, "", $unknownImagePath);
    }

}