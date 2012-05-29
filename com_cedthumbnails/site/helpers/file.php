<?php
/**
 * @package Component cedThumbnails for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');


class comCedThumbnailsFile
{
    var $resizeImageFilename = null;

    public function setResizeImageFilename($image, $extension)
    {
        $this->resizeImageFilename = $extension . "-" . md5($image) . ".jpg"; //TODO may want to keep original file type
    }

    public function getResizeImageFilename()
    {
        return $this->resizeImageFilename;
    }

    public function isCached($image, $extension)
    {
        $this->setResizeImageFilename($image, $extension);
        return JFile::exists(JPATH_SITE.'cache/'.$this->getResizeImageFilename());
    }

    public function filter($image, $defaultImage)
    {
        if ((strpos($image, "http://") === 0) ||
            (strpos($image, "www.") === 0)
        ) {
            $imageUrl = $image;
        } else {
            $imageUrl = JURI :: base() . $image;
        }

        if (JFile::exists($imageUrl)) {
            return $imageUrl;
        } else {
            if ($this->imageExist($imageUrl)) {
                return $imageUrl;
            }
        }

        return $defaultImage;
    }

    /**
     * http://stackoverflow.com/questions/981954/how-can-one-check-to-see-if-a-remote-file-exists-using-php/982045#982045
     * @param $file
     * @return mixed
     */
    private function imageExist($file)
    {
        //only tries to read 1 byte
        //return file_get_contents($file,0,null,0,1);

        // getimagesize. Unlike file_exists, this built-in function supports
        // remote files. It will return an array that contains the image information (width, height, type..etc)
        $imageArray = getimagesize($file);
        return $imageArray[0];
    }
}