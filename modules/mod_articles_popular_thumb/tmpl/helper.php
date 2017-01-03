<?php
/**
 * @package     cedThumbnails
 * @subpackage  mod_articles_popular_thumb
 *
 * @copyright   Copyright (C) 2013-2015 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

defined('_JEXEC') or die;

require_once JPATH_SITE.'/components/com_content/helpers/route.php';

JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_content/models', 'ContentModel');

/**
 * Helper for mod_articles_popular
 *
 * @package     Joomla.Site
 * @subpackage  mod_articles_popular
 */
abstract class modMostReadThumbHelper
{
	public static function getList(&$params, $cachePathId)
	{
		// Get an instance of the generic articles model
		$model = JModelLegacy::getInstance('Articles', 'ContentModel', array('ignore_request' => true));

		// Set application parameters in model
		$app = JFactory::getApplication();
		$appParams = $app->getParams();
		$model->setState('params', $appParams);

		// Set the filters based on the module params
		$model->setState('list.start', 0);
		$model->setState('list.limit', (int) $params->get('count', 5));
        $model->setState('list.select', 'a.fulltext, a.id, a.title, a.alias, a.introtext, a.state, a.catid, a.created, a.created_by, a.created_by_alias,' .
            ' a.modified, a.modified_by, a.publish_up, a.publish_down, a.images, a.urls, a.attribs, a.metadata, a.metakey, a.metadesc, a.access,' .
            ' a.hits, a.featured');

		$model->setState('filter.published', 1);
		$model->setState('filter.featured', $params->get('show_front', 1) == 1 ? 'show' : 'hide');

		// Access filter
		$access = !JComponentHelper::getParams('com_content')->get('show_noauth');
		$authorised = JAccess::getAuthorisedViewLevels(JFactory::getUser()->get('id'));
		$model->setState('filter.access', $access);

		// Category filter
		$model->setState('filter.category_id', $params->get('catid', array()));

		// Filter by language
		$model->setState('filter.language', $app->getLanguageFilter());

		// Ordering
        if ($params->get('show_period', 0)) {
            $model->setState('list.ordering', 'a.hits');
        }
        else {
            $period = $params->get('period_months', 6);
            $model->setState('list.ordering', ' a.hits / (DATEDIFF(CURRENT_DATE(),a.publish_up)+'.$period.') ' );
        }

		$model->setState('list.direction', 'DESC');

		$items = $model->getItems();

		foreach ($items as &$item)
		{
			$item->slug = $item->id.':'.$item->alias;
			$item->catslug = $item->catid.':'.$item->category_alias;

			if ($access || in_array($item->access, $authorised))
			{
				// We know that user has the privilege to view the article
				$item->link = JRoute::_(ContentHelperRoute::getArticleRoute($item->slug, $item->catslug));
			} else {
				$item->link = JRoute::_('index.php?option=com_users&view=login');
			}
		}

        require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/decorator.php';
        return cedThumbnailsDecorator::decorate($params, $items, "Mod_CedThumbnailsPopular-".$cachePathId);
    }

    /**
     * Add stylesheet to document <head>
     */
    public static function addStyleSheet($layout)
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::base().'/media/mod_articles_popular_thumb/' . substr($layout, 2) . ".css?v=2.9.2");
    }

}
