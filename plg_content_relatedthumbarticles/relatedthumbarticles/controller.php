<?php

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');

require_once(dirname(__FILE__) . DS . 'model.php');
require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';

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

        $this->demoMode = intval($params->get('demo', 0));
        $this->date_format = $params->get('date_format', '%Y-%m-%d');
        $this->thumbnailHeight = intval($params->get('thumbnailHeight', '70'));
        $this->useThumbnails = intval($params->get('useThumbnails', 1));
        $this->useDefaultImage = intval($params->get('useDefaultImage', 1));

        $this->model = new relatedThumbArticlesModel($params);

    }

    public function execute($articleId, $categoryId, $access)
    {

        $items = $this->model->getRelatedArticleRows($articleId, $categoryId, $access);

        //if we have something to do
        $html = "";
        if ($items) {

            $showDateInDays = intval($this->params->get('showDateInDays', 1));
            $comCedThumbnailsHelper = new comCedThumbnailsHelper();

            $entries = array();
            foreach ($items as $item) {
                $entry = array();
                $entry['created'] = JHTML::_('date', $item->created, $this->date_format);
                $entry['description'] = $comCedThumbnailsHelper->getDescription($this->params, $item->introtext);
                $entry['link'] = $comCedThumbnailsHelper->getArticleLink($item);
                $entry['title'] = $comCedThumbnailsHelper->getTitle($this->params, $item->title);
                $entry['dateAgo'] = $comCedThumbnailsHelper->getDateRepresentation($this->params, $item->created, $showDateInDays);
                $entry['image'] = $comCedThumbnailsHelper->getImage($this->params, $item);
                $entry['imgSrc'] = $comCedThumbnailsHelper->getResizedImageSource($this->params, $entry['image'], "relatedthumbarticles");
                $entry['width'] = intval($this->params->get('thumbnailWidth', 70));
                $entry['height'] = intval($this->params->get('thumbnailHeight', 70));

                $entries[] = $entry;
            }

            $rendering = strval($this->params->get('style', 'horizontal'));
            $class = relatedThumbArticlesController::pluginsFactory($rendering);
            $html = $class->render($entries, $this->params);
        }
        return $html;
    }


    public static function pluginsFactory($rendering)
    {
        $filename = dirname(__FILE__) . '/rendering/' . strtolower($rendering) . '.php';
        if (include_once($filename)) {
            $classname = 'CedThumbnails' . $rendering . 'Rendering';
            return new $classname;
        } else {
            throw new Exception('rendering not found');
        }
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
                $html .= '<li>
                     <img  style="width: 215px;" src="' . $entry['imgSrc'] . '" width="' . $entry['width'] . '" height="' . $entry['height'] . '" alt="' . $entry['title'] . '" title="' . $entry['title'] . '"/>
                     </li>';
            }
            $html .= '</ul>';

            $html .= $comCedThumbnailsHelper->addFooter('plgRelatedThumbnailAccordionTextAfter', $this->textAfter);
            $html .= '<div class="plgRelatedThumbnailAccordion_clear"></div>';
            $html .= '</div>';


            $document->addScriptDeclaration("window.addEvent('domready', function() { slideMenu.build('" . $uuid . "',600,10,10)} );");
        }
        return $html;
    }


}