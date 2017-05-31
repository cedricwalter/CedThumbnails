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

require_once(dirname(__FILE__) . '/models/thumbnails.php');

class relatedThumbArticlesController
{

	var $rendering = null;

	var $searchModel = null;
	var $thumbModel = null;

	protected function __construct($params)
	{
		$this->searchModel = $this->modelsFactory($params->get('searchMode', 'JoomlaTag'));
		$this->thumbModel  = new CedThumbnailsThumbnailsModel($params);
		$this->rendering   = $this->renderingFactory(strval($params->get('style', 'horizontal')));
		$this->demoMode    = $params->get('demo', '1');
	}

	public static function getInstance($params)
	{
		static $instance = null;
		if (null === $instance)
		{
			$instance = new static($params);
		}

		return $instance;
	}

	public function execute($params, $articleId, $categoryId)
	{
		//delegate to searchModel to find potential post
		$items = $this->searchModel->getModel($params, $articleId, $categoryId);

		if ($items)
		{
			// get thumbnails
			$viewModels = $this->thumbModel->getViewModel($this->searchModel, $items, $params);

			if ($this->demoMode)
			{
				$html = $this->getHtmlInDemoMode($params, $viewModels);

				return $html;
			}

			if ($params->get('useWebfont', false)) {
				$document = JFactory::getDocument();
				$css = "https://fonts.googleapis.com/css?family=".$params->get('googleFont', 'Niconne');
				$document->addStyleSheet($css);
			}

			return $this->rendering->render($viewModels, $params, JText::_('CEDTHUMBNAILS_RELATED_POSTS'));
		}

		return null;
	}

	public function addResources()
	{
		$this->rendering->addResources();
	}

	private function renderingFactory($rendering)
	{
		$fileName = dirname(__FILE__) . '/rendering/' . strtolower($rendering) . '.php';
		if (include_once($fileName))
		{
			$className = 'CedThumbnails' . $rendering . 'Rendering';

			return new $className;
		}
		else
		{
			throw new Exception('rendering not found');
		}
	}

	private function modelsFactory($type)
	{
		$fileName = dirname(__FILE__) . '/models/' . strtolower($type) . '.php';
		if (include_once($fileName))
		{
			$className = 'CedThumbnails' . $type . 'Model';

			return new $className;
		}
		else
		{
			throw new Exception('model type ' . $type . ' not found');
		}
	}

	/**
	 * @param $params
	 * @param $entries
	 *
	 * @return string
	 */
	private function getHtmlInDemoMode($params, $entries)
	{
		$html = "<h1>CedThumbnails in Demo mode</h1>";
		$html .= "<h2>" . JText::_('CEDTHUMBNAILS_RELATED_POSTS') . "</h2>";

//        jimport('joomla.image.image');
//        $JImage = new JImage("C:\Users\cedric\Dropbox\phpstart\www\DEV32\images\joomla black.gif");
//        $resizeJImage = $JImage->resize(50, 50, true, JImage::SCALE_FILL);
//
//        $options = array('quality' => 85);
//        $resizeJImage->toFile("C:\Users\cedric\Dropbox\phpstart\www\DEV32\images\joomla blackNEW.gif", IMAGETYPE_JPEG, $options);

		$html .= $this->renderingFactory('bigeffect1')->render($entries, $params, "Big Effect 1");
		$this->renderingFactory('bigeffect1')->addResources();

		$html .= $this->renderingFactory('bigeffect2')->render($entries, $params, "Big Effect 2");
		$this->renderingFactory('bigeffect2')->addResources();

		$html .= $this->renderingFactory('bigeffect3')->render($entries, $params, "Big Effect 3");
		$this->renderingFactory('bigeffect3')->addResources();

		$html .= $this->renderingFactory('horizontal')->render($entries, $params, "Horizontal");
		$this->renderingFactory('horizontal')->addResources();

		$html .= $this->renderingFactory('vertical')->render($entries, $params, "Vertical");
		$this->renderingFactory('vertical')->addResources();

		$html .= $this->renderingFactory('css3hover')->render($entries, $params, "CSS3 Hover");
		$this->renderingFactory('css3hover')->addResources();

		$html .= $this->renderingFactory('css3stack')->render($entries, $params, "CSS3 Stacked Elements");
		$this->renderingFactory('css3stack')->addResources();

		$html .= $this->renderingFactory('modernsimple')->render($entries, $params, "CSS3 simple caption");
		$this->renderingFactory('modernsimple')->addResources();

		$html .= $this->renderingFactory('modernfullcaption')->render($entries, $params, "CSS3 full caption");
		$this->renderingFactory('modernfullcaption')->addResources();

		$html .= $this->renderingFactory('modernfade')->render($entries, $params, "CSS3 fade caption");
		$this->renderingFactory('modernfade')->addResources();

		$html .= $this->renderingFactory('modernslidecaption')->render($entries, $params, "CSS3 slide caption");
		$this->renderingFactory('modernslidecaption')->addResources();

		$html .= $this->renderingFactory('modernrotate')->render($entries, $params, "CSS3 modern rotate caption");
		$this->renderingFactory('modernrotate')->addResources();

		$html .= $this->renderingFactory('modernscale')->render($entries, $params, "CSS3 modern scale caption");
		$this->renderingFactory('modernscale')->addResources();

		$html .= $this->renderingFactory('more')->render($entries, $params, "Magazine");
		$this->renderingFactory('more')->addResources();

		$html .= $this->renderingFactory('trendy')->render($entries, $params, "Trendy");
		$this->renderingFactory('trendy')->addResources();

		$html .= $this->renderingFactory('trendy2')->render($entries, $params, "Trendy 2");
		$this->renderingFactory('trendy2')->addResources();

		$html .= $this->renderingFactory('trendy3')->render($entries, $params, "Trendy 3");
		$this->renderingFactory('trendy3')->addResources();

//		$html .= $this->renderingFactory('accordion')->render($entries, $params, "Accordion");
//		$html .= $this->renderingFactory('caroussel')->render($entries, $params, "Caroussel");


		return $html;
	}
}