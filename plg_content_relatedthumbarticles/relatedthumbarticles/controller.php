<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

require_once(dirname(__FILE__) . DS . 'model.php');
require_once JPATH_SITE . '/components/com_cedthumbnails/helper.php';

class relatedThumbArticlesController extends JObject
{
    var $showDescription = 1;
    var $thumbnailWidth = 90; //px
    var $thumbnailHeight = 90; //px
    var $textBefore = null;
    var $textAfter = null;
    var $date_format = null;
    var $showDateInDays = null;
    var $showDate = null;
    var $searchMode = null;
    var $methodName = "horizontal";
    var $model = null;
    var $params = null;

    function relatedThumbArticlesController($params)
    {
        $this->params = $params;

        $this->showDescription = $params->get('showDescription', '1');
        $this->demoMode = intval($params->get('demo', 0));
        $this->textBefore = $params->get('textbefore', 'Related Posts');
        $this->date_format = $params->get('date_format', '%Y-%m-%d');
        $this->thumbnailWidth = intval($params->get('thumbnailWidth', '70'));
        $this->thumbnailHeight = intval($params->get('thumbnailHeight', '70'));
        $this->useThumbnails = intval($params->get('useThumbnails', 1));
        $this->useDefaultImage = intval($params->get('useDefaultImage', 1));

        $this->model = new relatedThumbArticlesModel($params);
        $this->methodName = strval($params->get('style', 'horizontal'));
    }

    public function execute($articleId, $categoryId, $access)
    {

        $items = $this->model->getRelatedArticleRows($articleId, $categoryId, $access);

        $showDateInDays = intval($this->params->get('showDateInDays', 1));
        //if we have something to do
        $html = "";
        if ($items) {
            $entries = array();
            foreach ($items as $item) {
                $entry = array();
                $entry['created'] = JHTML::_('date', $item->created, $this->date_format);
                $entry['description'] = comCedThumbnailsHelper::getDescription($this->params, $item->introtext);
                $entry['link'] = comCedThumbnailsHelper::getArticleLink($item);
                $entry['title'] = comCedThumbnailsHelper::getTitle($this->params, $item->title);
                $entry['dateAgo'] = comCedThumbnailsHelper::getDateRepresentation($this->params, $item->created, $showDateInDays);
                $entry['image'] = comCedThumbnailsHelper::getImage($this->params, $item);
                $entry['imgSrc'] = comCedThumbnailsHelper::getResizedImageSource($this->params, $entry['image'], "relatedthumbarticles");

                $entries[] = $entry;
            }

            if ($this->demoMode) {
                $html .= '<h2>Vertical Demo</h2>';
                $html .= $this->vertical($entries) . '<br />';
                $html .= '<h2>Horizontal Demo</h2>';
                $html .= $this->horizontal($entries) . '<br />';
                $html .= '<h2>Matrix Demo</h2>';
                $html .= $this->matrix($entries) . '<br />';

                if (relatedThumbArticlesHelper::isJoomla15() == false) {
                    $html .= '<h2>RelatedPostsSlideOuts (JQuery 1.5) Demo</h2>';
                    $html .= "See on the left side! Hover to slide out" . $this->relatedPostsSlideOuts($entries) . '<br />';
                }
                $html .= '<h2>Vertical Accordion with Mootools Demo</h2>';
                $html .= "See on the left side! Hover to slide out" . $this->accordionVerticalMootols($entries) . '<br />';
            } else {
                //  dynamically call a method on an object it is not necessary to use call_user_function
                $html = $this->{
                //"emptyPlugin"
                $this->methodName
                }($entries);
            }
        }
        return $html;
    }


    function emptyPlugin($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document = JFactory::getDocument();
            $document->addStyleSheet('media/plg_content_relatedthumbitems/newsticker.css');
            $document->addScript("media/plg_content_relatedthumbitems/newsticker.js");
            $document->addScriptDeclaration("var hor = new Ticker('TickerVertical',{speed:1000,delay:4000,direction:'horizontal'});");
            $html = "<!-- powered by related thumb items www.waltercedric.com -->
                <div id='NewsTicker' class='NewsTicker'>
                  <h1> Visit</h1>
	            <div class='NewsVertical' id='NewsVertical'>
	            <ul id='TickerVertical' class='TickerVertical' >";
            foreach ($entries as $entry) {
                $html .= "<li>
                            <img src='http://localhost/dev17/images/sampledata/fruitshop/apple.jpg' width='70' height='70' border='0' class='NewsImg'/>
                            <span class='NewsTitle'>
                                <a href='http://woorktuts.110mb.com/newsticker/pic/N0003.png'>Fire Destroys Historic English Pier</a>
                            </span>
                            The Weston Grand at Weston-super-Mare was an iconic destination for vacationers for more than a century.
                            <span class='NewsFooter'><strong>Published July 25</strong></span>
                        </li>";

            }
            $html .= "    </ul>
                       </div>
                    </div>";
        }

        return $html;
    }


    /**
     * http://www.jquerynewsticker.com/
     *
     * @param $entries
     * @return string
     */
    function jqueryNewsTicker($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
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
            $html .= "</div>" . comCedThumbnailsHelper::addFooter('rtim_after', $this->textAfter) . "</div>";
            $html .= "</div>";
        }

        return $html;
    }

    /** all credits to http://tympanus.net/Tutorials/RelatedPostsSlideOuts/
     *
     * @param $entries
     * @return string
     */
    function relatedPostsSlideOuts($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document = JFactory::getDocument();
            $document->addStyleSheet('media/plg_content_relatedthumbitems/relatedPostsSlideOuts.css');
            $document->addScript("https://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js");
            $document->addScriptDeclaration("var jQuery = jQuery.noConflict();");
            $document->addScript("media/plg_content_relatedthumbitems/relatedPostsSlideOuts.js");

            $html = "<!-- powered by related thumb items www.waltercedric.com -->
                <div id='rp_list' class='rp_list'>
                <ul>";
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
            $html .= "</div>" . comCedThumbnailsHelper::addFooter('rtim_after', $this->textAfter) . "</div>";
            $html .= "</div>";

        }

        return $html;
    }

    function accordionVerticalMootols($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document = JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/accordionvertical.css");

            $html = '
					<div class="plgRelatedThumbnailAccordionVertical">
					  <div class="plgRelatedThumbnailAccordionVerticalTextBefore">' . $this->textBefore . '</div>';
            $html .= '<div id="accordion">';

            foreach ($entries as $entry) {
                $html .= '<h3 class="plgRelatedThumbnailAccordionVerticalToggler">' . $entry['title'] . '</h3>
		  <div class="plgRelatedThumbnailAccordionVerticalElement">
		    <a href="' . $entry['url'] . '"><img src="' . $entry['imgSrc'] . '" class="plgRelatedThumbnailAccordionVerticalImg" /></a>
		    <p>
		      <span class="plgRelatedThumbnailAccordionVerticalDesc">' . $entry['description'] . '</span>
		      <span class="plgRelatedThumbnailAccordionVerticalDateAgo">' . $entry['dateAgo'] . '</span>
		    </p>
		    <a href="' . $entry['url'] . '" class="plgRelatedThumbnailAccordionVerticalUrl">Read more</a>
		  </div>';
            }

            $html .= '</div>';
            $html .= comCedThumbnailsHelper::addFooter('plgRelatedThumbnailAccordionVerticalTextAfter', $this->textAfter);
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
        $html .= "</div>" . comCedThumbnailsHelper::addFooter('rtim_after', $this->textAfter) . "</div>";

        return $html;

    }


    /**
     * idea from http://www.2webvideo.com/video-production/horizontal-accordion-with-mootools
     * and http://www.leigeber.com/2008/05/horizontal-javascript-accordion-menu/
     *
     *
     * Enter description here ...
     * @param unknown_type $entries
     */
    function accordionWithoutMootols($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document = JFactory::getDocument();
            $uuid = uniqid();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/slidemenu.css");
            $document->addScript(JURI :: base() . "media/plg_content_relatedthumbitems/slidemenu.js");

            $html = "<div class='plgRelatedThumbnailAccordion'>";
            $html .= "<div class='plgRelatedThumbnailAccordionTextBefore'>" . $this->textBefore . "</div>";

            $html .= '<ul id="' . $uuid . '" class="plgRelatedThumbnailAccordion" >';
            foreach ($entries as $entry) {
                $html .= '<li><img  style="width: 215px;" src="' . $entry['imgSrc'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '"/></li>';
            }
            $html .= '</ul>';

            $html .= comCedThumbnailsHelper::addFooter('plgRelatedThumbnailAccordionTextAfter', $this->textAfter);
            $html .= '<div class="plgRelatedThumbnailAccordion_clear"></div>';
            $html .= '</div>';


            $document->addScriptDeclaration("window.addEvent('domready', function() { slideMenu.build('" . $uuid . "',600,10,10)} );");
        }
        return $html;
    }

    function horizontal($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document =& JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/horizontal.css");
            $html = '<!-- powered by related thumb items www.waltercedric.com -->
					<div class="rtih">
					 <div class="rtih_before">' . $this->textBefore . '</div>
		               <div class="rtih_bloc">';


            foreach ($entries as $entry) {
                $html .= '<div class="rtih_entry"
							 style="width: ' . ($this->thumbnailWidth + 10) . 'px;">
							  <a class="rtih_link" href="' . $entry['link'] . '">
							      <img class="rtih_img" src="' . $entry['imgSrc'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '"/>
							      <div class="rtih_ago">' . $entry['dateAgo'] . '</div>
								  <div class="rtih_title">' . $entry['title'] . '</div>
							  </a>';
                if ($this->showDescription) {
                    $html .= '<div class="rtih_desc">' . $entry['description'] . '</div>';
                }
                $html .= '</div>';
            }
            $html .= '<div class="rtih_clear"></div>';
            $html .= '</div>';
            $html .= comCedThumbnailsHelper::addFooter('rtih_footer', $this->textAfter);
            $html .= '</div>';
        }

        return $html;
    }


    /**
     * return a N x M matrix of pictures
     *
     * @param $entries the lsit of entries to render
     * @return string the html dom
     */
    function matrix($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document =& JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/matrix.css");

            $html = "<!-- powered by related thumb items www.waltercedric.com -->
			         <div class='rtim'>
			            <div class='rtim_before'>" . $this->textBefore . "</div>
                        <div class='rtih_bloc'>";
            $i = 0;
            foreach ($entries as $entry) {
                $html .= '<div class="rtim_entry">
                              <a href="' . $entry['link'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '">
                                <img src="' . $entry['imgSrc'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '"/>
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
            $html .= comCedThumbnailsHelper::addFooter('rtim_after', $this->textAfter);
            $html .= "</div>";
        }

        return $html;
    }

    function vertical($entries)
    {
        $html = "";
        if (sizeof($entries) > 0) {
            $document =& JFactory::getDocument();
            $document->addStyleSheet("media/plg_content_relatedthumbitems/vertical.css");
            $html = "<!-- powered by related thumb items www.waltercedric.com -->
					  <div class='rtiv'>
					 <div class='rtiv_before'>" . $this->textBefore . "</div>
					   <div class='rtiv_bloc'>";

            foreach ($entries as $entry) {
                $html .= '<div class="rtiv_entry" style="height: 74px;">
							 <a href="' . $entry['link'] . '" target="_top">
							   <img class="rtiv_img" src="' . $entry['imgSrc'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '"/>
							   <div class="rtiv_title"><a href="' . $entry['link'] . '" target="_top">' . $entry['title'] . '</div></a>';

                if ($this->showDescription) {
                    $html .= '<div class="rtih_desc">' . $entry['description'] . '</div>';
                }
                $html .= "<div class='rtiv_ago'>" . $entry['dateAgo'] . "</div>
						</div>";
            }
            $html .= "</div>" . comCedThumbnailsHelper::addFooter('rtiv_after', $this->textAfter) . "</div>";
        }
        return $html;
    }


}