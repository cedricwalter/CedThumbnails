<?php
/**
 * @package Component cedThumbnails for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class comCedThumbnailsJIMage
{

    private function resize($image, $newImage, $width, $height)
    {
        try {
            jimport('joomla.image.image');
            $jImage = new JImage($image);
            $jImage->resize($width, $height, true, JImage::SCALE_INSIDE);
            $jImage->toFile($newImage);
        }
        catch (Exception $e) {
            error_log("resizeWithJImage: while processing image " . $image . " exception occured " . $e);
        }
        unset($jImage);
    }


}
