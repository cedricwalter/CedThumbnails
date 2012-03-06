<?php

/**
 * @version		$Id: script.php 22596 2011-12-22 15:25:19Z github_bot $
 * @package		Joomla.Administrator
 * @subpackage	com_admin
 * @copyright	Copyright (C) 2005 - 2011 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.database.table');

/**
 * Script file of joomla CMS
 */
class lib_wideimageInstallerScript
{
	/**
	 * method to preflight the update of Joomla!
	 *
	 * @param	string          $route      'update' or 'install'
	 * @param	JInstallerFile  $installer  The class calling this method
	 *
	 * @return void
	 */
	public function preflight($route, $installer)
	{
		echo "cedric was here";
		/*
		$db = JFactory::getDbo();
		$db->setQuery("delete from '#__extensions' where name = 'wideimage' and type = 'library';");
		$db->query();*/
		return true;
	}

}
