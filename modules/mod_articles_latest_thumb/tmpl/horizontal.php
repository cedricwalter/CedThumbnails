<?php
/**
 * @package     cedThumbnails
 * @subpackage  mod_articles_latest_thumb
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="cedth-latest-horiz<?php echo $moduleclass_sfx; ?>">
    <?php foreach ($list as $item) : ?>
        <div class="cedth-latest-horiz-entry" style="width: <?php echo($params->get('thumbnailWidth') + 10) ?>px;">
            <a class="cedth-latest-horiz-link" href="<?php echo $item->link; ?>">
                <?php if ($params->get('useThumbnails')) { ?>
                    <img class="cedth-latest-horiz-img"
                         src="<?php echo $item->imgSrc; ?>"
                         alt="<?php echo $item->alt; ?>"
                         title="<?php echo $item->caption; ?>"
                         width="<?php echo $params->get('thumbnailWidth') ?>"
                         height="<?php echo $params->get('thumbnailHeight') ?>"
                        />
                <?php } ?>
                <div class="cedth-latest-horiz-ago"></div>
                <div class="cedth-latest-horiz-title"><?php echo $item->title; ?></div>
            </a>
            <?php if ($params->get('useTeaser')) { ?>
                <div class="cedth-latest-horiz-desc"><?php echo $item->teaser; ?> <?php echo $params->get('teaserEnding'); ?></div>
            <?php } ?>
        </div>
    <?php endforeach; ?>
    <div class="clearfix"/>
    <?php
    $comCedThumbnailsHelper = new comCedThumbnailsHelper();
    echo $comCedThumbnailsHelper->addFooter();
    ?>
</div>
