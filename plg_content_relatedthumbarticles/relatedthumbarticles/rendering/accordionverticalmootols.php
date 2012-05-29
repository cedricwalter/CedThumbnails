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

class CedThumbnailsAccordionverticalmootolsRendering extends JObject
{

    public function render($entries, $params)
    {
        $showDescription = $params->get('showDescription', '1');
        $thumbnailWidth = intval($params->get('thumbnailWidth', '70'));
        $textBefore = $params->get('textbefore', 'Related Posts');
        $textAfter = $params->get('textAfter', '');

        $html = "";
        if (sizeof($entries) > 0) {
            $document = JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/accordionvertical.css");

            $html = '
					<div class="plgRelatedThumbnailAccordionVertical">
					  <div class="plgRelatedThumbnailAccordionVerticalTextBefore">' . $textBefore . '</div>';
            $html .= '<div id="accordion">';

            foreach ($entries as $entry) {
                $html .= '<h3 class="plgRelatedThumbnailAccordionVerticalToggler">' . $entry['title'] . '</h3>
		  <div class="plgRelatedThumbnailAccordionVerticalElement">
		    <a href="' . $entry['link'] . '">
		      <img src="' . $entry['imgSrc'] . '"  width="' . $entry['width'] . '" height="' . $entry['height'] . '" class="plgRelatedThumbnailAccordionVerticalImg" /></a>
		    <p>
		      <span class="plgRelatedThumbnailAccordionVerticalDesc">' . $entry['description'] . '</span>
		      <span class="plgRelatedThumbnailAccordionVerticalDateAgo">' . $entry['dateAgo'] . '</span>
		    </p>
		    <a href="' . $entry['link'] . '" class="plgRelatedThumbnailAccordionVerticalUrl">Read more</a>
		  </div>';
            }

            $html .= '</div>';
            $html .= comCedThumbnailsHelper::addFooter('plgRelatedThumbnailAccordionVerticalTextAfter', $textAfter);
            $html .= '</div>';
            $document->addScriptDeclaration("window.addEvent('domready', function() {
  /* var accordion = new Accordion($$('.plgRelatedThumbnailAccordionVerticalToggler'),$$('.plgRelatedThumbnailAccordionVerticalElement'), { pre-MooTools More */
  var accordion = new Fx.Accordion($$('.plgRelatedThumbnailAccordionVerticalToggler'),$$('.plgRelatedThumbnailAccordionVerticalElement'), {
    opacity: 0,
    onActive: function(toggler) { toggler.setStyle('color', '#f30'); },
    onBackground: function(toggler) { toggler.setStyle('color', '#000'); }
  });
});");
        }

        return $html;
    }
}