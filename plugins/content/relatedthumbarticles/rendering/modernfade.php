<?php
/**
 * @package     cedThumbnails
 * @subpackage  plg_content_relatedthumbarticles
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */


// no direct access
defined('_JEXEC') or die('Restricted access');

require_once(dirname(__FILE__) . '/renderinginterface.php');

class CedThumbnailsModernFadeRendering implements renderingInterface
{
    public function __construct()
    {
    }

    //http://www.hongkiat.com/blog/css3-image-captions/
    public function render($entries, $params, $title = "")
    {
    }

    public function addResources()
    {
        $document = JFactory::getDocument();
        $document->addStyleSheet(JUri::base().'/media/plg_content_relatedthumbarticles/modern.css');
    }

}
