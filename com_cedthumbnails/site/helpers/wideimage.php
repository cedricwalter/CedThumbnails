<?php
/**
 * @package Component cedThumbnails for Joomla! 2.5
 * @author waltercedric.com
 * @copyright (C) 2012 http://www.waltercedric.com
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html v3.0
 **/

// no direct access
defined('_JEXEC') or die('Restricted access');

class comCedThumbnailsWideIMage
{


    public function resize($image, $newImage, $width, $height)
    {
        require_once(JPATH_SITE . '/libraries/wideimage/WideImage.php');
        try {
            // fill mean do not keep aspect ratio
            $image = WideImage::load($image);
            $image->resize($width, $height, 'fill')->saveToFile($newImage);
            unset($image);
        }
        catch (Exception $e) {
            error_log("resizeWideImage: while processing image " . $image . " exception occured " . $e);
        }
    }


}
