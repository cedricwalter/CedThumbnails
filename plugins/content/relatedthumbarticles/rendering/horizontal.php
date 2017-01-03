<?php
/**
 * @package     cedThumbnails
 * @subpackage  plg_content_relatedthumbarticles
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';
require_once(dirname(__FILE__) . '/renderinginterface.php');

class CedThumbnailsHorizontalRendering implements renderingInterface
{

    public function __construct()
    {
    }

    public function render($entries, $params, $title = "")
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $showDescription = $params->get('showDescription', '1');
            $thumbnailWidth = "".intval($params->get('thumbnailWidth', '70')) + 10 ;
            $thumbnailWidth = $thumbnailWidth."px;";

            $html = '<!-- Copyright (C) 2013-2016 galaxiis.com All rights reserved. -->
            <h2>'.$title.'</h2>';
            $html .= '<div class="rtih_clear"></div>';
            $html .= '<div class="rtih">
   		               <div class="rtih_bloc">';
            $useThumbnails = intval($params->get('useThumbnails', 1));

            foreach ($entries as $entry) {
                    $html .= "<div class=\"rtih_entry\"
   							 >
   							  <a class=\"rtih_link\" href=$entry->link>";
                if ($useThumbnails)
                    $html .= "<img class=\"rtih_img\"
   							           src=\"$entry->imgSrc\"
   							           width=\"$entry->width\"
   							           height=\"$entry->height\"
   							           alt=\"$entry->alt\"
   							           title=\"$entry->caption\"/>";

                    $html .= "<div class=\"rtih_ago\">$entry->dateAgo</div>
   								  <div class=\"rtih_title\">$entry->title</div>
   							  </a>";
                if ($showDescription) {
                    $html .= '<div class="rtih_desc" style="width: 210px;">' . $entry->description . '</div>';
                }
                $html .= '</div>';
            }
            $html .= '<div class="rtih_clear"></div>';
            $html .= '</div>';
            $comCedThumbnailsHelper = new comCedThumbnailsHelper();
            $html .= $comCedThumbnailsHelper->addFooter();
            $html .= '</div>';
        }

        return $html;
    }

    public function addResources()
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::base().'/media/plg_content_relatedthumbarticles/horizontal.css?v=2.9.2');
    }
}