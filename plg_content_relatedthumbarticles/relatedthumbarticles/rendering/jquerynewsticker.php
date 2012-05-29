<?php
/**
 * @version        CedThumbnails
 * @package
 * @copyright    Copyright (C) 2009 Cedric Walter. All rights reserved.
 * @copyright    www.cedricwalter.com / www.waltercedric.com
 *
 * @license        GNU/GPL, see LICENSE.php
 *
 * CedThumbnails is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';

class CedThumbnailsJquerynewstickerRendering  extends JObject
{


    /**
     * http://www.jquerynewsticker.com/
     *
     * @param $entries
     * @return string
     */
    function render($entries, $params)
    {
        $html = "";
        if (sizeof($entries) > 0) {

            $showDescription = $params->get('showDescription', '1');
            $thumbnailWidth = intval($params->get('thumbnailWidth', '70'));
            $textBefore = $params->get('textbefore', 'Related Posts');
            $textAfter = $params->get('textAfter', '');

            $document = JFactory::getDocument();
            $document->addStyleSheet('media/plg_content_relatedthumbitems/ticker-style.css');
            $document->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
            $document->addScriptDeclaration("var jQuery = jQuery.noConflict();");
            $document->addScript("media/plg_content_relatedthumbitems/jquery.ticker.js");

            $html = "<!-- powered by related thumb items www.waltercedric.com -->
                    <div id='rp_list' class='rp_list'>
                    <ul class='js-hidden''>";
            foreach ($entries as $entry) {
                $html .= "<li>
                            <div>
                                 <a href='" . $entry['link'] . "' alt='" . $entry['title'] . "'><img src='" . $entry['imgSrc'] . "' alt='" . $entry['title'] . "'/></a>
                                <span class='rp_title'>" . $entry['title'] . "</span>
                                <span class='rp_links'>
                                    <a href='" . $entry['link'] . "' alt='" . $entry['title'] . "'>Article</a>
                                </span>
                            </div>
                        </li> ";
            }
            $html .= "</ul>
                    <span id='rp_shuffle' class='rp_shuffle'></span>";
            $html .= "</div>" . comCedThumbnailsHelper::addFooter('rtim_after', $textAfter) . "</div>";
            $html .= "</div>";
        }

        return $html;
    }
}