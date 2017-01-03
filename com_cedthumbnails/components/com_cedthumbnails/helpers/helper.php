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
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.error.log');
jimport('joomla.version');
jimport('joomla.filesystem.file');

class comCedThumbnailsHelper
{

	public function __construct()
	{
	}

	static function param($name, $default = '')
	{
		static $params;
		if (!isset($params))
		{
			$params = JComponentHelper::getParams('com_cedthumbnails');
		}

		return $params->get($name, $default);
	}

	/**
	 * remove all html tags and return a truncate text of text
	 *
	 * @param $params
	 * @param $introText
	 * @param $fullText
	 *
	 * @return string
	 * @internal param $text
	 */
	public function getDescription($params, $introText, $fullText)
	{
		$teaser = null;
		if ($params->get('useTeaser', 1))
		{

			$length = $params->get('teaserLength');
			$ending = $params->get('teaserEnding');

			if (strlen($introText) > 1)
			{
				$teaser = $this->trimAndKeepOnlyCompleteWord($introText, $length, $ending);
			}
			else
			{
				$teaser = $this->trimAndKeepOnlyCompleteWord($fullText, $length, $ending);
			}
		}

		return $teaser;
	}

	public function getTitle($title, $useTitle = 1, $length = 60)
	{
		$safeTitle = null;
		if ($useTitle)
		{
			$safeTitle = htmlspecialchars($title);
			$safeTitle = $this->trimAndKeepOnlyCompleteWord($safeTitle, $length, "");
		}

		return $safeTitle;
	}

	//Cut multibyte string to n symbols and add delimiter but do not break words.
	public function trimAndKeepOnlyCompleteWord($string, $chars = 50, $terminate = "")
	{
		// remove links
		//$text = preg_replace('/<a href=\"(.*?)\">(.*?)<\/a>/', "\\2", $string);
		//$text = preg_replace('/<a.*?<\/a>/','',$string);

		//strip all HTML tags, not invalid html code may lead to text truncated
		$text = strip_tags($string);
		//Strip eventual mambots code,  Remove all text between two symbols { }
		$text = preg_replace('/{[^}]*}/', '', $text);

		//remove control characters
		$text = str_replace(array("\r\n", "\r", "\n", '&nbsp;'), " ", $text);

		// remove multiple spaces
		$text = trim(preg_replace('/ {2,}/', ' ', $text));

		return $this->truncateWords($text, $chars, $terminate);
	}

	private function maxChars($string, $limit, $break = " ", $pad = "...")
	{
		$charset = 'UTF-8';
		if (mb_strlen($string, $charset) <= $limit)
		{
			return $string;
		}
		if (false !== ($breakpoint = strpos($string, $break, $limit)))
		{
			if ($breakpoint < mb_strlen($string, $charset) - 1)
			{
				$string = mb_substr($string, 0, $breakpoint, $charset) . $pad;

			}
		}

		return $string;
	}

	// http://www.the-art-of-web.com/php/truncate/
	private function truncateWords($input, $numberWords, $padding = "...")
	{
		$output = strtok($input, " ");
		while (--$numberWords > 0)
		{
			$output .= " " . strtok(" ");
		}
		if ($output != $input)
		{
			$output .= $padding;
		}

		return $output;
	}


	private function trimAndKeepOnlyCompleteWord3($string, $chars = 50, $terminate = "")
	{
		//strip all HTML tags, not invalid html code may lead to text truncated
		$text = strip_tags($string);
		//Strip eventual mambots code,  Remove all text between two symbols { }
		$text = preg_replace('/{[^}]*}/', '', $text);

		$text = str_replace('\n', " ", $text);
		$text = str_replace('\r', " ", $text);

		$chars -= mb_strlen($terminate, 'UTF-8');
		if ($chars <= 0)
		{
			return $terminate;
		}

		$text = mb_substr($text, 0, $chars);

		//Find position of first occurrence of " " in text
		$space = mb_strrpos($text, ' ');

		if ($space < mb_strlen($text, 'UTF-8'))
		{
			return $text . $terminate;
		}

		return mb_substr($text, 0, min($space, $chars)) . $terminate;
	}

	public function isExcluded($params)
	{

		// Check if menu items have been excluded
		if ($exclusions = $params->get('menu_items', array()))
		{
			// Get the current menu item
			$active = JFactory::getApplication()->getMenu()->getActive();

			if ($active && $active->id && in_array($active->id, (array) $exclusions))
			{
				return true;
			}
		}

		// Check if regular expressions are being used
		if ($exclusions = $params->get('exclude', ''))
		{
			// Normalize line endings
			$exclusions = str_replace(array("\r\n", "\r"), "\n", $exclusions);

			// Split them
			$exclusions = explode("\n", $exclusions);

			// Get current path to match against
			$path = JUri::getInstance()->toString(array('path', 'query', 'fragment'));

			// Loop through each pattern
			if ($exclusions)
			{
				foreach ($exclusions as $exclusion)
				{
					// Make sure the exclusion has some content
					if (strlen($exclusion))
					{
						if (preg_match('/' . $exclusion . '/is', $path, $match))
						{
							return true;
						}
					}
				}
			}
		}

		return false;
	}

	public function isActiveInCategory($params, $categoryId)
	{
		$categoryMode       = intval($params->get('categoryMode', 0));
		$selectedCategories = $params->get('includedCatIds');

		if ($categoryMode == 0)
		{
			return true;
		}

		if ($categoryMode == 1)
		{
			if ($selectedCategories == null)
			{
				return false;
			}

			return $this->isSelectedInCategory($selectedCategories, $categoryId);
		}

		return !$this->isSelectedInCategory($selectedCategories, $categoryId);
	}

	private function isSelectedInCategory($selectedCategories, $categoryId) {
		$match = false;
		if (is_array($selectedCategories))
		{
			foreach ($selectedCategories as $category)
			{
				if ($category === "")
				{ // all category is in the list
					return true;
				}
				if (strcmp(trim($category), $categoryId) == 0)
				{
					$match = true;
				}
			}
		}
		return $match;
	}

	public function getDateRepresentation($params, $articleDate, $showDateInDays = true)
	{
		$showDate    = $params->get('showDate', 1);
		$date_format = $params->get('date_format', 'Y-m-d');

		return $this->getDateRepresentation2($showDate, $articleDate, $showDateInDays, $date_format);
	}


	private function getDateRepresentation2($showDate = true, $articleDate, $showDateInDays = true, $date_format)
	{
		$date = "";
		if ($showDate)
		{
			if ($showDateInDays)
			{
				$JConfig = new JConfig;
				$nowDate = date('Y-m-d H:i:s', time() + $JConfig->offset * 60 * 60);
				$date .= comCedThumbnailsHelper::dateDiff($articleDate,
						$nowDate) . " " . JText::_('CEDTHUMBNAILS_DAY_AGO');
			}
			else
			{
				$date .= JHTML::_('date', $articleDate, $date_format);
			}
		}

		return $date;
	}


	public function addFooter()
	{
		//Please keep this link,
		//consider buying a license if you would like to remove, you're not forced to donate!
		//back-links are my only salary
		$html = "<!-- cedThumnbnails Joomla! by http://www.galaxiis.com/ -->";

		//Think about all the effort it took to create this extensions, and get a license instead of just removing this link :-)
		$removeCopyright = false;

		if (!$removeCopyright)
		{
			$html .= '<div style="text-align: center;">
        		<a
        		title="CedThumbnails related posts with thumbnails! CedThumbnails is a blog widget that appears under each post, linking to related stories from your blog archive."
        		href="https://www.galaxiis.com/cedthumbnails-showcase/" style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; " onmouseover="this.style.textDecoration=\'underline\'" onmouseout="this.style.textDecoration=\'none\'" target="_blank"><b>powered by CedThumbnails</b></a>
        	</div>';
		}

		return $html;
	}

	/**
	 * Finds the difference in days between two calendar dates. require PHP 5.1 at least
	 *
	 * @param $startDate date
	 * @param $endDate   date
	 *
	 * @return float
	 */
	public function dateDiff($startDate, $endDate)
	{
		if (function_exists('gregoriantojd'))
		{
			// Parse dates for conversion
			$startArray = date_parse($startDate);
			$endArray   = date_parse($endDate);

			// Convert dates to Julian Days
			$start_date = gregoriantojd($startArray["month"], $startArray["day"], $startArray["year"]);
			$end_date   = gregoriantojd($endArray["month"], $endArray["day"], $endArray["year"]);

			// Return difference
			return round(($end_date - $start_date), 0);
		}
		else
		{
			//the one above is better!
			return round((strtotime($endDate) - strtotime($startDate)) / 86400);
			//return "<font color='red'>public function gregoriantojd() do not exist! upgrade to php 5.1 or use in relatedArticles admin panel showDateInDays = NO as a workaround</font>";
		}
	}

	public function getImageAlt($title, $alt)
	{
		return $alt == null ? htmlspecialchars($title) : htmlspecialchars($alt);
	}

	public function getImageCaption($title, $caption)
	{
		return $caption == null ? htmlspecialchars($title) : htmlspecialchars($caption);
	}
}