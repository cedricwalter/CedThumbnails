<?php
/**
 * @version        CedThumbnails
 * @package
 * @copyright    Copyright (C) 2009 Cedric Walter. All rights reserved.
 * @copyright    www.cedricwalter.com / www.waltercedric.com
 *
 * @license        GNU/GPL, see LICENSE.php
 *
 * CedThumbnails is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<div class="mostreadthumbmatrix<?php echo $moduleclass_sfx; ?>">
    <!-- Popular Articles with Thumbnails by Cedric Walter - www.waltercedric.com -->
<?php
    $i = 0;
    foreach ($list as $item) :
        ?>
        <div class="entry">
            <?php
            if ($item->imageSrc != null) {
            ?>
            <a href="<?php echo $item->item->link; ?>">
                <img id="thumb" align="left" class="image"
                     alt="<?php echo $item->item->title; ?>"
                     title="<?php echo $item->item->title; ?>"
                     src="<?php echo $item->imageSrc; ?>">
            </a>
            <?php

        }     ?>
        </div>
            <?php
                    $i++;
        if ($i == $params->get('matrixSize')) {
            ?>
            <div class="mostreadthumbmatrixclear"></div>
            <?php
            $i = 0;
        }
        ?>


        <?php endforeach; ?>
    <div class="mostreadthumbmatrixclear"></div>
    <div style="text-align: center;">
		<a href="http://www.waltercedric.com" style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; " onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>CedThumbnails</b></a>
	</div>
</div>