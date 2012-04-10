<?php
/**
 * @version      2.5.1  
 * @package      PhotoFeed
 * @copyright    Copyright (C) 2009-2012 Cedric Walter. All rights reserved.
 * @copyright    www.cedricwalter.com / www.waltercedric.com
 *
 * @license        GNU/GPL v3.0, see LICENSE.php
 *
 * PhotoFeed is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

require_once(dirname(__FILE__) . DS . 'relatedthumbarticles/controller.php');
require_once JPATH_SITE . '/components/com_cedthumbnails/helper.php';

class plgContentRelatedThumbArticles extends JPlugin
{

    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
        if (comCedThumbnailsHelper::isJoomla15()) {
            $plugin =& JPluginHelper::getPlugin('content', 'relatedthumbarticles');
            $this->pluginsParams = new JParameter($plugin->params);
        } else {
            $this->pluginsParams = $this->params;
            $this->loadLanguage();
            $this->controller = new relatedThumbArticlesController($this->pluginsParams);
        }
        $this->controller = new relatedThumbArticlesController($this->pluginsParams);
    }

    var $controller = null;
    var $pluginsParams = null;
    
    //Joomla 1.5 entry point
    function onPrepareContent(&$article, &$params, $limitstart)
    {
        return $this->execute($article);
    }

    //Joomla 1.6 / 1.7 entry point
    public function onContentPrepare($context, &$row, &$params, $page = 0)
    {
        return $this->execute($row);
    }

    private function execute(&$row)
    {
        global $access;

        //optimization, escape fast
        if (!$this->pluginsParams->get('enabled', 1)) {
            return true;
        }

        $view = JRequest::getString('view');
        if ($view == 'article' && array_key_exists('catid', $row)) {
            if (comCedThumbnailsHelper::isActiveInCategory($this->pluginsParams, $row) == false) {
                return true;
            }
            
            $html = $this->controller->execute($row->id, $row->catid, $access);

            $positioning = intval($this->pluginsParams->get('position', 1));
            if ($positioning) {
                $row->text = $row->text . $html;
            } else {
                $row->text = $html . $row->text;
            }
        }
        return true;
    }
}

?>