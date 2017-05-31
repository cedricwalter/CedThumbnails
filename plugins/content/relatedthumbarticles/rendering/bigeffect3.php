<?php
/**
 * @package     cedThumbnails
 * @subpackage  plg_content_relatedthumbarticles
 *
 * @copyright   CedThumbnails 3.1.3 - Copyright (C) 2013-2017 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id          1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

require_once JPATH_SITE . '/components/com_cedthumbnails/helpers/helper.php';
require_once(dirname(__FILE__) . '/renderinginterface.php');

class CedThumbnailsbigeffect3Rendering implements renderingInterface
{

	public function __construct()
	{
	}

	public function render($viewModels, $params, $title = "")
	{
		$html = array();
		if (sizeof($viewModels) > 0)
		{
//			width: $viewModel->width; height:$viewModel->height
			$useTeaser     = $params->get('useTeaser', '1');
			$useDate       = $params->get('useDate', '1');
			$useThumbnails = intval($params->get('useThumbnails', 1));
			$useTitle      = intval($params->get('useTitle', 1));
			$debug      = intval($params->get('debug', 1));

			$thumbnailWidth = "" . intval($params->get('thumbnailWidth', '70')) + 10;
			$thumbnailWidth = $thumbnailWidth . "px;";

			$html[] = '<!-- CedThumbnails 3.1.3 - Copyright (C) 2013-2017 galaxiis.com All rights reserved. -->';
			$html[] = '<h2>' . $title . '</h2>';
			$html[] = '<div class="grid-wrap">';

			foreach ($viewModels as $viewModel)
			{
				$html[] = "
				<a class=\"list-block demo-3\" href=\"$viewModel->link\">
    <figure>
      <img src=\"$viewModel->resizedImage\" alt=\"$viewModel->alt\" style=\"width: $viewModel->width; height:$viewModel->height\"/>
      <figcaption>
        <h2>$viewModel->title</h2>
        <p>$viewModel->description</p>
      </figcaption>
    </figure>
  </a>";
			}
			$html[] = '</div>';
			$html[] = '<div style="clear: both;"></div>';
			$comCedThumbnailsHelper = new comCedThumbnailsHelper();
			$html[] = $comCedThumbnailsHelper->addFooter();
		}

		return implode("\n", $html);
	}

	public function addResources()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base() . '/media/plg_content_relatedthumbarticles/bigeffect1.css?v=3.1.4');
	}
}