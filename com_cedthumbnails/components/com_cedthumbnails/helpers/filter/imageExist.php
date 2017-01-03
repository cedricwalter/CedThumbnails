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

class imageExist extends Filter
{

    private $successor;

    public function setSuccessor($nextFilter)
    {
        $this->successor = $nextFilter;
    }

    public function handleRequest(&$unknownImagePath)
    {
        $unknownImagePath = JPath::clean($this->serverRootPath . "/" . $unknownImagePath);

        if (!$this->imageExisting($unknownImagePath)) {
            $unknownImagePath = null;
        }

        return $unknownImagePath;
    }


    /**
     * http://stackoverflow.com/questions/981954/how-can-one-check-to-see-if-a-remote-file-exists-using-php/982045#982045
     * @param $file
     * @return mixed
     */
    private function imageExisting($file)
    {
        try {
            //only tries to read 1 byte
            //return file_get_contents($file,0,null,0,1);

            // getimagesize. Unlike file_exists, this built-in function supports
            // remote files. It will return an array that contains the image information (width, height, type..etc)
            $imageArray = getimagesize($file);

            return $imageArray[0];
        } catch (Exception $e) {
            error_log("cedThumbnails, following image '" . $file . "' do not exist, or cant be read " . $e);

            return false;
        }
    }

}