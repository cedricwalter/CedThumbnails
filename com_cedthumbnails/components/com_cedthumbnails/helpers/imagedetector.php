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

require_once(dirname(__FILE__) . '/imagemodelfactory.php');
require_once(dirname(__FILE__) . '/log.php');
require_once(JPATH_SITE . '/administrator/components/com_cedthumbnails/helpers/helper.php');


class comCedThumbnailsImageDetector
{
    const INTRO_TEXT_INTRO_IMAGE_FULL_TEXT_FULL_ARTICLE_IMAGE = 0;
    const INTRO_TEXT = 1;
    const INTRO_TEXT_INTRO_IMAGE = 11;
    const INTRO_IMAGE = 111;
    const FULLTEXT = 2;
    const FULLTEXT_FULL_ARTICLE_IMAGE = 22;
    const FULL_ARTICLE_IMAGE = 222;
    const INTRO_TEXT_FULLTEXT = 3;
    const FULLTEXT_INTRO_TEXT = 4;

    var $CedThumbnailsHelper = null;

    public function __construct()
    {
        $this->CedThumbnailsHelper = new CedThumbnailsHelper();
    }

    /**
     * @param $params
     * @param $item
     * @return mixed|null|stdClass|string
     */
    public function getImage($params, $item)
    {
        //not all posts have a introText or fulltext
        $introText = array_key_exists('introtext', $item) ? $item->introtext : "";
        $fullText = array_key_exists('fulltext', $item) ? $item->fulltext : $introText;
        $articleMetadataString = $item->images;
        $originThumbnails = intval($params->get('originThumbnails', 1));

        $image = $this->getImageModel($params, $introText, $fullText, $articleMetadataString, $originThumbnails, $item);

        //avoid generating unique image/filename if $image model is the default empty image
        if (!$image->isDefault) {
            $image->fileName = $item->title;
        }

        return $image;
    }


    /**
     * @param $params
     * @param $introText
     * @param $fullText
     * @param $articleMetadataString
     * @param $originThumbnails
     * @param $item
     * @return null|stdClass
     */
    public function getImageModel($params, $introText, $fullText, $articleMetadataString, $originThumbnails, $item)
    {
        comCedThumbnailsLog::log("trying to detect an image in article '$item->title'");

        $article_metadata = json_decode($articleMetadataString);
        $image = null;

        switch ($originThumbnails) {
            case self::INTRO_TEXT_INTRO_IMAGE_FULL_TEXT_FULL_ARTICLE_IMAGE: //search in intro text -> use intro image -> in full text -> use full article image
                comCedThumbnailsLog::log("search image in intro text -> use 'intro image' -> in full text -> use 'full article image'");
                $image = $this->getImageFrom($introText);
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in intro text, search now in intro image");
                    $image = comCedThumbnailsImageModelFactory::buildFromIntroTextMeta($article_metadata);
                }
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in 'intro image' -> search now in full text");
                    $image = $this->getImageFrom($fullText);
                    $image->caption = $item->title;
                }
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in full text -> search now in 'full article image'");
                    $image = comCedThumbnailsImageModelFactory::buildFromFullTextMeta($article_metadata);
                }

                break;
            case self::INTRO_TEXT: //search in intro text only
                comCedThumbnailsLog::log("search image in intro text only");
                $image = $this->getImageFrom($introText);
                $image->caption = $item->title;
                break;
            case self::INTRO_TEXT_INTRO_IMAGE: //search in intro text -> use intro image
                comCedThumbnailsLog::log("search in intro text -> use 'intro image'");
                $image = $this->getImageFrom($introText);
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in intro text, search now in 'intro image'");
                    $image = comCedThumbnailsImageModelFactory::buildFromIntroTextMeta($article_metadata);
                }
                break;
            case self::INTRO_IMAGE: //search in intro image
                comCedThumbnailsLog::log("search image in 'intro image' only");
                $image = comCedThumbnailsImageModelFactory::buildFromIntroTextMeta($article_metadata);
                break;
            case self::FULLTEXT: //search in full text only
                comCedThumbnailsLog::log("search image in full text only");
                $image = $this->getImageFrom($fullText);
                break;
            case self::FULLTEXT_FULL_ARTICLE_IMAGE: //search in full text -> use full article image
                comCedThumbnailsLog::log("search in full text -> use full article image");
                $image = $this->getImageFrom($fullText);
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in full text, search now in 'full article image");
                    $image = comCedThumbnailsImageModelFactory::buildFromFullTextMeta($article_metadata);
                }
                break;
            case self::FULL_ARTICLE_IMAGE: //search in full article image only
                comCedThumbnailsLog::log("search in full article image only");
                $image = comCedThumbnailsImageModelFactory::buildFromFullTextMeta($article_metadata);
                break;
            case self::INTRO_TEXT_FULLTEXT: //search in intro text but if not found try in full text
                comCedThumbnailsLog::log("search in intro text but if not found try in full text");
                $image = $this->getImageFrom($introText);
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in intro text, search now in full text");
                    $image = $this->getImageFrom($fullText);
                }
                $image->caption = $item->title;
                break;
            case self::FULLTEXT_INTRO_TEXT: //search in full text but if not found try in intro text
                $image = $this->getImageFrom($fullText);
                comCedThumbnailsLog::log("search in full text but if not found try in intro text");
                if ($image->url == null) {
                    comCedThumbnailsLog::log("not found in full text, search now in intro text");
                    $image = $this->getImageFrom($introText);
                }
                $image->caption = $item->title;
                break;
        }

        if ($image->url == null) {
            comCedThumbnailsLog::log("image still not found use now default (blank) image");
            $image = comCedThumbnailsImageModelFactory::buildDefaultImageModel($params);
            $image->caption = $item->title;
        }
        comCedThumbnailsLog::log("image found will use '$image->url' for creating thumbnails ");
        return $image;
    }

    /**
     * @param $params
     * @param $html
     * @return mixed
     */
    public function getImageFrom($html)
    {
        $image = comCedThumbnailsImageModelFactory::buildEmptyImageModel();
        return $this->getImageFromWith($image, $html);
    }

    /**
     * @param $image
     * @param $html
     * @return mixed
     */
    public function getImageFromWith($image, $html)
    {
        //cant use a DOM XML parser, not all code is XHTML valid, do a string index before a regexp which cost more time
        if (strpos($html, "img")) {
            //$pattern = "/img.*src=[\"']?([^\"']?.*)[\"']?/i";
            $pattern = "/\<img.+?src=\"(.+?)\".+?/";
            preg_match_all($pattern, $html, $images);

            if (count($images) >= 2) {
                $allImages = $images[1];
                if (count($allImages) >= 1) {
                    $image->url = $allImages[0];
                }
            }
        }


        return $image;
    }

}