<?php
/**
 * @version        1.5.0
 * @package        last article module with thumbails
 * @copyright    Copyright (C) 2011 Cedric Walter from www.waltercedric.com. All rights reserved.
 * @license       GNU General Public License version 3 or later; see LICENSE.txt
 *
 * relatedThumbsArticles is free software. This version may have been modified pursuant
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
<div class="latestnews<?php echo $moduleclass_sfx; ?>">
    <!-- Most read articles with thumbnails by Cedric Walter - www.waltercedric.com -->
    <?php foreach ($list as $item) : ?>
    <div class="mostreadthumbentry">
        <span class='mostreadthumb_image'>
			<?php
            if ($item->image != null) {
                ?>
                <a href="<?php echo $item->item->link; ?>">
                    <img id="mostreadthumb_thumb" align="left"
                         alt="<?php echo $item->item->title; ?>"
                         title="<?php echo $item->item->title; ?>"
                         src="<?php echo JURI :: base() . "libraries/timthumb/timthumb.php?src=" . $item->image . "&amp;w=" . $params->get('thumbnailWidth') . "&amp;h=" . $params->get('thumbnailHeight') . "&amp;zc=".$params->get('thumbnailZoomCrop'); ?>">
                </a>
                <?php
            }     ?>
		</span>
        <?php
        if ($params->get('useTitle')) {
         ?>
		<span class="mostreadthumb_title">
			<a href="<?php echo $item->item->link; ?>"><?php echo $item->item->title; ?></a>
		</span>
        <?php
        }
        ?>
        <?php if ($item->teaser != null) { ?>
        <span class="mostreadthumb_teaser">
			<?php echo $item->teaser; ?><?php echo $params->get('teaserEnding'); ?>
		</span>
        <?php } ?>
    </div>
    <div style="clear:both;"></div>
    <?php endforeach; ?>
    <div style="text-align: center;">
		<a href="http://www.waltercedric.com" style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; " onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>CedThumbnails</b></a>
	</div>
</div>
