<?php
/**
 * @package     cedThumbnails
 * @subpackage  com_cedthumbnails
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 * @id 1c7495e0-ayx7-11e3-8b68-0800200c9a66
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');
jimport('joomla.application.component.helper');

class CedThumbnailsHelper extends JObject
{

    public function __construct()
    {
        parent::__construct();
    }

    public static function getComponentVersion()
    {
        static $version;

        if (!isset($version)) {
            $xmlFile = JPATH_ADMINISTRATOR . '/components/com_cedthumbnails/com_cedthumbnails.xml';
            if (file_exists($xmlFile)) {
                $xml = JFactory::getXML($xmlFile);
                $version = (string)$xml->version;
            }
        }
        return $version;
    }

    public function isImageInSafeHosts($imageUrl)
    {
        if (CedThumbnailsHelper::param("useSafehosts", 0)) {
            foreach (CedThumbnailsHelper::getSafeHosts() as $safe) {
                if ($this->startsWith($imageUrl, $safe)) {
                    return true;
                }
            }
            return false;
        }
        return true;
    }

    function startsWith($haystack, $needle)
    {
        return !strncmp($haystack, $needle, strlen($needle));
    }

    function endWith($haystack, $needle)
    {
        return substr($haystack, -strlen($needle)) === $needle;
    }

    public static function getSafeHosts()
    {
        $safeHosts = trim(CedThumbnailsHelper::param("safeHosts", ""));
        if ($safeHosts != null) {
            return explode(";", $safeHosts);
        }
        return array();
    }

    static function param($name, $default = '')
    {
        static $params;
        if (!isset($params)) {
            $params = JComponentHelper::getParams('com_cedthumbnails');
        }

        return $params->get($name, $default);
    }


}
