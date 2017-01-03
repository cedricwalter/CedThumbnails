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

//chain-of-responsibility pattern
abstract class Filter {

    var $hostname = null;
    var $liveSiteUrl = null;
    var $serverRootPath = null;

    function __construct($liveSiteUrl, $serverRootPath)
    {
        $this->hostname = parse_url($liveSiteUrl, PHP_URL_HOST);
        $this->liveSiteUrl = $liveSiteUrl;
        $this->serverRootPath = $serverRootPath;
    }

    abstract public function handleRequest(&$imageModel);

    abstract public function setSuccessor($nextService);

    protected function startsWith($haystack, $needle)
    {
        return $needle === "" || strpos($haystack, $needle) === 0;
    }
}