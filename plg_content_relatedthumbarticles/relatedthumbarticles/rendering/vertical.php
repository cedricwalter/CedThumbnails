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

class CedThumbnailsVerticalRendering extends JObject
{


    public function render($entries, $params)
    {
        $showDescription = $params->get('showDescription', '1');
        $thumbnailHeight = intval($params->get('thumbnailHeight', '70'));
        $textBefore = $params->get('textbefore', 'Related Posts');
        $textAfter = $params->get('textAfter', '');

        $html = "";
        if (sizeof($entries) > 0) {
            $document = JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/vertical.css");
            $html = "<!-- powered by related thumb items www.waltercedric.com -->
        					  <div class='rtiv'>
        					 <div class='rtiv_before'>" . $textBefore . "</div>
        					   <div class='rtiv_bloc'>";

            foreach ($entries as $entry) {
                $html .= '<div class="rtiv_entry"
                            style="height: ' . ($thumbnailHeight + 10) . 'px;">
        							 <a href="' . $entry['link'] . '" target="_top">
        							   <img class="rtiv_img"
        							        src="' . $entry['imgSrc'] . '"
        							        width="' . $entry['width'] . '"
        							        height="' . $entry['height'] . '"
        							        alt="' . $entry['title'] . '"
        							        title="' . $entry['title'] . '"/>
        							   <div class="rtiv_title"><a href="' . $entry['link'] . '" target="_top">' . $entry['title'] . '</div></a>';

                if ($showDescription) {
                    $html .= '<div class="rtih_desc">' . $entry['description'] . '</div>';
                }
                $html .= "<div class='rtiv_ago'>" . $entry['dateAgo'] . "</div>
        						</div>";
            }
            $html .= "</div>" . comCedThumbnailsHelper::addFooter('rtiv_after', $textAfter) . "</div>";
        }
        return $html;
    }
}