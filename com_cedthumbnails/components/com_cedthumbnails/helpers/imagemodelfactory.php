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

require_once(dirname(__FILE__) . '/helper.php');

class comCedThumbnailsImageModelFactory
{

    /**
     * user may still enter an invalid default image, so we filter and fallback to default settings if needed
     * @param $params
     * @return mixed|string
     */
    public static function buildDefaultImageModel($params)
    {
        $image = new stdClass();
        $image->url = $params->get('defaultImage');
        $image->alt = htmlspecialchars($params->get('defaultImageAlt'));
        $image->caption = htmlspecialchars($params->get('defaultImageCaption'));
        $image->fileName = "default";
        $image->isDefault = true;

        return $image;
    }

    public static function buildEmptyImageModel()
    {
        $image = new stdClass();
        $image->url = null;
        $image->alt = null;
        $image->caption = null;
        $image->filename = null;
        $image->isDefault = false;

        return $image;
    }

    public static function buildFromFullTextMeta($articleFullTextMetaData)
    {
        if ($articleFullTextMetaData != null && $articleFullTextMetaData->image_fulltext != null) {
            $image = new stdClass();
            $image->url = $articleFullTextMetaData->image_fulltext;
            $image->alt = htmlspecialchars($articleFullTextMetaData->image_fulltext_alt);
            $image->caption = htmlspecialchars($articleFullTextMetaData->image_fulltext_caption);
            $image->filename = null;
            $image->isDefault = false;
            return $image;
        }
        return comCedThumbnailsImageModelFactory::buildEmptyImageModel();
    }

    public static function buildFromIntroTextMeta($articleIntroTextMetaData)
    {
        //IntroText is always null in joomla 3.1
        if ($articleIntroTextMetaData != null && $articleIntroTextMetaData->image_intro != null) {
            $image = new stdClass();
            $image->url = $articleIntroTextMetaData->image_intro;
            $image->alt = htmlspecialchars($articleIntroTextMetaData->image_intro_alt);
            $image->caption = htmlspecialchars($articleIntroTextMetaData->image_intro_caption);
            $image->filename = null;
            $image->isDefault = false;
            return $image;
        }

        return comCedThumbnailsImageModelFactory::buildEmptyImageModel();
    }

}
