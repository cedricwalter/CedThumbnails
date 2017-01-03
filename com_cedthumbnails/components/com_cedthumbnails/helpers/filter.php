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

require_once(dirname(__FILE__) . '/log.php');
require_once(dirname(__FILE__) . '/filter/imageStartsWithLiveSiteRemoval.php');
require_once(dirname(__FILE__) . '/filter/liveSiteWithoutSchemeRemoval.php');
require_once(dirname(__FILE__) . '/filter/wwwRemoval.php');
require_once(dirname(__FILE__) . '/filter/imageExist.php');
require_once(dirname(__FILE__) . '/filter/hostnameRemoval.php');

jimport('joomla.filesystem.path');

/**
 * Class comCedThumbnailsFilter
 *
 * images from articles may be invalid or worse not an image
 */
class comCedThumbnailsFilter
{

    var $serverRootPath = null;
    var $liveSiteUrl = null;
    var $defaultImageModel = null;

    public static $filter = null;

    function __construct($serverRootPath, $liveSiteUrl, $defaultImageModel)
    {
        $this->serverRootPath = $serverRootPath;
        $this->liveSiteUrl = $liveSiteUrl;
        $this->defaultImageModel = $defaultImageModel;
    }


    /**
     * return a filtered url in $imageModel->url or either the $defaultImageModel
     * @param $imageModel
     *
     * @return null
     */
    public function filter($imageModel)
    {
        $imageStartsWithLiveSiteRemoval = $this->getFilter($this->liveSiteUrl, $this->serverRootPath);

        $unknownImagePath = $imageStartsWithLiveSiteRemoval->handleRequest($imageModel->url);

        if (isset($unknownImagePath)) {
            $imageModel->path = $unknownImagePath;

            return $imageModel;
        } else {
            return $this->defaultImageModel;
        }
    }

    private function getFilter($liveSiteUrl, $serverRootPath)
    {
        $imageStartsWithLiveSiteRemoval = new imageStartsWithLiveSiteRemoval($liveSiteUrl, $serverRootPath);
        $liveSiteWithoutSchemeRemoval =  new liveSiteWithoutSchemeRemoval($liveSiteUrl, $serverRootPath);
        $hostnameRemoval = new hostnameRemoval($liveSiteUrl, $serverRootPath);
        $wwwRemoval = new wwwRemoval($liveSiteUrl, $serverRootPath);
        $imageExist = new imageExist($liveSiteUrl, $serverRootPath);

        $imageStartsWithLiveSiteRemoval->setSuccessor($liveSiteWithoutSchemeRemoval);
        $liveSiteWithoutSchemeRemoval->setSuccessor($hostnameRemoval);
        $hostnameRemoval->setSuccessor($wwwRemoval);
        $wwwRemoval->setSuccessor($imageExist);

        return $imageStartsWithLiveSiteRemoval;
    }


}