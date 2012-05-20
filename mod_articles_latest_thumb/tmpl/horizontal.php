<?php
/**
 * Created by JetBrains PhpStorm.
 * User: cedric
 * Date: 4/10/12
 * Time: 11:00 PM
 * To change this template use File | Settings | File Templates.
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="latest-posts<?php echo $moduleclass_sfx; ?>">
    <!-- Latest Articles with Thumbnails by Cedric Walter - www.waltercedric.com -->
    <div class="rtih">
        <div class="rtih_bloc">
            <?php foreach ($list as $item) : ?>
            <div class="rtih_entry"
                 style="width: <?php echo ($params->get('thumbnailWidth') + 10)?>px;">
                <a class="rtih_link" href="<?php echo $item->item->link; ?>">
                    <?php if ($params->get('useThumbnails')) { ?>
                        <img class="rtih_img" src="<?php echo $item->imageSrc; ?>" alt="<?php echo $item->item->title; ?>"
                           title="<?php echo $item->item->title; ?>"/>
                    <?php } ?>

                    <div class="rtih_ago"></div>
                    <div class="rtih_title"><?php echo $item->item->title; ?></div>
                </a>
                <?php if ($params->get('useTeaser')) { ?>
                <div class="rtih_desc"><?php echo $item->teaser; ?> <?php echo $params->get('teaserEnding'); ?></div>
                <?php } ?>
            </div>
            <?php endforeach; ?>
            <div class="rtih_clear"></div>
        </div>
    </div>
    <div style="text-align: center;">
        <a href="http://www.waltercedric.com"
           style="font: normal normal normal 10px/normal arial; color: rgb(187, 187, 187); border-bottom-style: none; border-bottom-width: initial; border-bottom-color: initial; text-decoration: none; "
           onmouseover="this.style.textDecoration='underline'" onmouseout="this.style.textDecoration='none'" target="_blank"><b>CedThumbnails</b></a>
    </div>
</div>
