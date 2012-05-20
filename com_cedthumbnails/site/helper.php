<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');
jimport('joomla.version');
jimport('joomla.filesystem.file');

class comCedThumbnailsHelper
{


    public function getResizedImageSource($params, $imagePathRelativeOrAbsolute, $extension = "")
    {
        $thumbnailWidth = intval($params->get('thumbnailWidth', 70));
        $thumbnailHeight = intval($params->get('thumbnailHeight', 70));
        $defaultImage = $params->get('defaultImage', "/media/plg_content_relatedthumbitems/default.jpg");
        return comCedThumbnailsHelper::getResizedImageSourceWith($defaultImage, $thumbnailWidth, $thumbnailHeight, $imagePathRelativeOrAbsolute, $extension);
    }


    public function getResizedImageSourceWith($defaultImage, $thumbnailWidth, $thumbnailHeight, $imagePathRelativeOrAbsolute, $extension = "")
    {
        //make a unique file per extension since some extensions may want to have different size of the same image
        //if startwith
        if ((strpos($imagePathRelativeOrAbsolute, "http://") === 0) ||
            (strpos($imagePathRelativeOrAbsolute, "www.") === 0)
        ) {
            $image = $imagePathRelativeOrAbsolute;
        } else {
            $image = JURI :: base() . $imagePathRelativeOrAbsolute;
        }

        $newImageFilename = $extension . "-" . md5($image) . ".jpg"; //TODO may want to keep original file type
        $newImage = JPATH_SITE . DS . 'cache' . DS . $newImageFilename;

        if (JFile::exists(JURI::Base() . 'cache/' . $newImageFilename) == false) {
            require_once(JPATH_SITE . DS . 'libraries' . DS . 'wideimage' . DS . 'WideImage.php');
            try {
                // fill mean do not keep aspect ratio
                WideImage::load($image)->resize($thumbnailWidth, $thumbnailHeight, 'fill')->saveToFile($newImage);
                return JURI::Base() . 'cache/' . $newImageFilename;
            }
            catch (Exception $e) {
                error_log("while processing image " . $image . " exception occured " . $e);
            }
            return comCedThumbnailsHelper::getResizedImageSourceWith($defaultImage, $thumbnailWidth, $thumbnailHeight, $defaultImage, $extension);
        }
    }

    public function getArticleLink($row2)
    {
        if (comCedThumbnailsHelper::isJoomla15()) {
            $url = str_replace('&amp;', '&', ContentHelperRoute::getArticleRoute($row2->slug, $row2->catslug, $row2->sectionid));
        } else {
            $url = JRoute::_(ContentHelperRoute::getArticleRoute($row2->slug, $row2->catslug));
        }

        $uri = JURI::getInstance();
        $prefix = $uri->toString(array('scheme', 'host', 'port'));
        $JConfig = new JConfig;
        return $JConfig->sef ? $prefix . JRoute::_($url) : $url;
    }


    /**
     * @param $params
     * @param $introText
     * @param $fullText
     * @return image|int|null
     */
    public
    function getImage($params, $item)
    {
        //escape fast
        if (!intval($params->get('useThumbnails', 1))) {
            $defaultImage = $params->get('defaultImage', "/media/plg_content_relatedthumbitems/default.jpg");
            return $defaultImage;
        }

        $introText = $item->introtext;
        //not all posts have a fulltext
        $fullText = array_key_exists('fulltext', $item) ? $item->fulltext : $introText;

        $originThumbnails = intval($params->get('originThumbnails', 1));
        $image = null;
        switch ($originThumbnails) {
            case 1: //search in intro text only
                $image = comCedThumbnailsHelper::getImageFrom($introText);
                break;
            case 2: //search in full text only
                $image = comCedThumbnailsHelper::getImageFrom($fullText);
                break;
            case 3: //search in intro text but if not found try in full text
                $image = comCedThumbnailsHelper::getImageFrom($introText);
                if ($image == null) {
                    $image = comCedThumbnailsHelper::getImageFrom($fullText);
                }
                break;
            case 4: //search in full text but if not found try in intro text
                $image = comCedThumbnailsHelper::getImageFrom($fullText);
                if ($image == null) {
                    $image = comCedThumbnailsHelper::getImageFrom($introText);
                }
                break;
        }

        $useDefaultImage = intval($params->get('useDefaultImage', 1));
        if ($image == null && $useDefaultImage) {

            return $params->get('defaultImage', "/media/plg_content_relatedthumbitems/default.jpg");
        }

        return $image;
    }

    /**
     * @param $html
     * @return image path
     */
    private
    function getImageFrom($html)
    {
        //cant use a DOM XML parser, not all code is XHTML valid, do a string index before a regexp which cost more time
        $image = null;
        if (strpos($html, "img")) {
            //$pattern = "/img.*src=[\"']?([^\"']?.*)[\"']?/i";
            $pattern = "/\<img.+?src=\"(.+?)\".+?\/>/";
            preg_match_all($pattern, $html, $images);

            if (count($images) >= 2) {
                $allimages = $images[1];
                if (count($allimages) >= 1) {
                    $image = $allimages[0];
                }
            }
        }
        if ($image == null && strpos($html, "http://www.youtube")) {
            //http://rubular.com/r/M9PJYcQxRW
            /*
             * youtube.com/v/{vidid}
             * youtube.com/vi/{vidid}
             * youtube.com/?v={vidid}
             * youtube.com/?vi={vidid}
             * youtube.com/watch?v={vidid}
             * youtube.com/watch?vi={vidid}
             * youtu.be/{vidid}
             */
            preg_match('#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#', $html, $images);
            $image = "http://i.ytimg.com/vi/".$images[0]."/0.jpg";
        }

        return $image;
    }


    /**
     * remove all html tags and return a truncate text of text
     * @param $text
     * @param $params
     * @return unknown_type
     */
    public
    function getDescription($params, $text)
    {
        $teaser = null;
        if ($params->get('useTeaser', 1)) {
            $length = $params->get('teaserLength');
            $descriptionLength = $params->get('teaserLimit', 60);
            $teaser = comCedThumbnailsHelper::trimAndKeepOnlyCompleteWord($text, $length, $params->get('teaserEnding'), $descriptionLength);
        }
        return $teaser;

    }

    public
    function getTitle($params, $title)
    {
        $title = htmlspecialchars($title);
        $titleLength = $params->get('titleLength', '60');

        return comCedThumbnailsHelper::trimAndKeepOnlyCompleteWord($title, $titleLength, $params->get('teaserEnding'));
    }


    /**
     * Cut string to n symbols and add delimiter but do not break words.
     *
     * Example:
     * <code>
     *  $string = 'this sentence is way too long';
     *  echo neat_trim($string, 16);
     * </code>
     *
     * Output: 'this sentence is...'
     *
     * @access private
     * @param string string we are operating with
     * @param integer character count to cut to
     * @param string|NULL delimiter. Default: '...'
     * @return string processed string
     **/
    private
    function trimAndKeepOnlyCompleteWord($string, $numberWords = 25, $delimiter = '...')
    {
        //strip all HTML tags, not invalid html code may lead to text truncated
        $trimmedString = strip_tags($string);
        //Strip eventual mambots code,  Remove all text between two symbols { }
        $trimmedString = preg_replace('/{[^}]*}/', '', $trimmedString);

        $trimmedString = str_replace('\n', " ", $trimmedString);
        $trimmedString = str_replace('\r', " ", $trimmedString);

        $trimmedString = substr($trimmedString, 0, $numberWords);

        /*
        $array = explode(' ', $trimmedString);
        $trimmedString = implode(' ', array_slice($array, 0, $numberWords));
        */
        if (strlen($string) > strlen($trimmedString)) {
            $trimmedString .= $delimiter;
        }


        return $trimmedString;
    }


    public
    function isActiveInCategory($params, &$row)
    {
        $selectedCategoryID = $params->get('includedCatIds');
        $articleCategoryID = $row->catid;
        $match = null;
        if (comCedThumbnailsHelper::isJoomla15()) {
            $match = (is_array($selectedCategoryID)) ? in_array($articleCategoryID, $selectedCategoryID)
                : strcmp(trim($selectedCategoryID), $articleCategoryID) == 0;
        } else {
            $match = (is_array($selectedCategoryID)) ? in_array($articleCategoryID, $selectedCategoryID)
                : strcmp(trim($selectedCategoryID), $articleCategoryID) == 0;
            //relatedThumbArticlesHelper::log('Article id <' . $articleCategoryID . '> is not in your selection of section selectedCategoryID <' . $selectedCategoryID . '>');
        }
        return $match;
    }

    /**
     * @param $params
     * @param $articleDate
     * @return string
     */
    public
    function getDateRepresentation($params, $articleDate, $showDateInDays = true)
    {
        $date = "";
        $showDate = $params->get('showDate', 1);
        if ($showDate) {
            if ($showDateInDays) {
                $JConfig = new JConfig;
                $nowDate = date('Y-m-d H:i:s', time() + $JConfig->offset * 60 * 60);
                $textDaysAgo = $params->get('textdaysago', "days ago");
                $date .= comCedThumbnailsHelper::dateDiff($articleDate, $nowDate) . " " . $textDaysAgo;
            }
            else {
                $date .= $articleDate;
            }
        }
        return $date;
    }


    /**
     * @return bool true if Joomla 1.5
     */
    public static
    function  isJoomla15()
    {
        if (version_compare(JVERSION, '1.7.0', 'ge')) {
            return false;
        }
        elseif (version_compare(JVERSION, '1.6.0', 'ge')) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     * @param $cssClass
     * @return string
     */
    public
    function addFooter($cssClass, $textAfter)
    {
        $html = '<div class="' . $cssClass . '">' . $textAfter . '</div>';
        //Please keep this link,
        //consider a donation if you would like to remoe, youre not forced to donate!
        //backlink is my only salary
        $html .= "<!-- related post items for Joomla! by http://www.waltercedric.com/ -->";
        $html .= '<div style="text-align: center;">
        		<a href="http://www.waltercedric.com" style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; " onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" target="_blank"><b>CedThumbnails</b></a>
        	</div>';
        return $html;
    }

    /**
     * Finds the difference in days between two calendar dates. require PHP 5.1 at least
     *
     * @param Date $startDate
     * @param Date $endDate
     * @return Int
     */
    public
    function dateDiff($startDate, $endDate)
    {
        if (function_exists('gregoriantojd')) {
            // Parse dates for conversion
            $startArray = date_parse($startDate);
            $endArray = date_parse($endDate);

            // Convert dates to Julian Days
            $start_date = gregoriantojd($startArray["month"], $startArray["day"], $startArray["year"]);
            $end_date = gregoriantojd($endArray["month"], $endArray["day"], $endArray["year"]);

            // Return difference
            return round(($end_date - $start_date), 0);
        }
        else {
            //the one above is better!
            return round((strtotime($endDate) - strtotime($startDate)) / 86400);
            //return "<font color='red'>public function gregoriantojd() do not exist! upgrade to php 5.1 or use in relatedArticles admin panel showDateInDays = NO as a workaround</font>";
        }
    }
}