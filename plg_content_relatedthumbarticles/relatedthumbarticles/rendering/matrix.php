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

class CedThumbnailsMatrixRendering extends JObject
{


    /**
     * return a N x M matrix of pictures
     *
     * @param $entries the lsit of entries to render
     * @return string the html dom
     */
    function render($entries, $params)
    {
        $html = "";
        if (sizeof($entries) > 0) {

            $textBefore = $params->get('textbefore', 'Related Posts');
            $textAfter = $params->get('textAfter', '');

            $document = JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/matrix.css");

            $html = "<!-- powered by related thumb items www.waltercedric.com -->
		         <div class='rtim'>
		            <div class='rtim_before'>" . $textBefore . "</div>
                       <div class='rtih_bloc'>";
            $i = 0;
            foreach ($entries as $entry) {
                $html .= '<div class="rtim_entry">
                             <a href="' . $entry['link'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '">
                               <img src="' . $entry['imgSrc'] . '"
                                 width="' . $entry['width'] . '"
                                 height="' . $entry['height'] . '"
                                 alt="' . $entry['title'] . '"
                                 title="' . $entry['title'] . '"/>
                             </a>
                         </div>';
                $i++;
                if ($i == 4) { //$this->params->matrixLimit) {
                    $html .= '<div class="rtim_clear"></div>';
                    $i = 0;
                }
            }
            $html .= '</div>'; //end rtih_bloc
            $html .= '<div class="rtim_clear"></div>';
            $html .= comCedThumbnailsHelper::addFooter('rtim_after', $textAfter);
            $html .= "</div>";
        }

        return $html;
    }

}