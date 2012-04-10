<?php
/**
 * @copyright    Copyright (C) 2011 Cedric Walter from www.waltercedric.com. All rights reserved.
 * @license        GNU/GPL, see LICENSE.php
 *
 * mod_articles_popular_thumb is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.

 * Author: Cedric Walter
 * Email: cedric.walter@gmail.com
 * Web: http://www.waltercedric.com
 **/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<!-- Popular Articles with Thumbnails by Cedric Walter - www.waltercedric.com -->
<div class="popular-posts<?php echo $moduleclass_sfx; ?>">
<ul>
    <?php foreach ($list as $item) : ?>
        <li>
        <div class="item-content">
            <?php if ($params->get('useThumbnails')) { ?>
                <div class="item-thumbnail">
                <a href="<?php echo $item->item->link; ?>" target="_blank">
                <img alt="<?php echo $item->item->title; ?>"
                     title="<?php echo $item->item->title; ?>"
                     border="0"
                     width="<?php echo $params->get('thumbnailWidth')?>"
                     height="<?php echo $params->get('thumbnailHeight')?>"
                     src="<?php echo $item->imageSrc; ?>">
                </a>
                </div>
            <?php } ?>
            <?php if ($params->get('useTitle')) { ?>
                <div class="item-title"><a href="<?php echo $item->item->link; ?>"><?php echo $item->title; ?></a></div>
            <?php } ?>
            <?php if ($params->get('useTeaser')) { ?>
                <div class="item-snippet"><?php echo $item->teaser; ?></div>
            <?php } ?>
        </div>
        <div style="clear: both;"></div>
        </li>
    <?php endforeach; ?>
</ul>
<div class="clear"></div>
    <div style="text-align: center;">
		<a href="http://www.waltercedric.com" style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; " onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>CedThumbnails</b></a>
	</div>
</div>