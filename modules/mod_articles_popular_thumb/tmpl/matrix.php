<?php
/**
 * @package     cedThumbnails
 * @subpackage  mod_articles_popular_thumb
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<div class="popular-matrix-posts<?php echo $moduleclass_sfx; ?>">
    <?php
    $i = 0;
    foreach ($list as $item) :
        ?>
        <div class="entry">
            <?php
            if ($item->imgSrc != null) {
                ?>
                <a href="<?php echo $item->link; ?>">
                    <?php if ($params->get('useThumbnails')) { ?>
                        <img
                                id="thumb"
                                align="left"
                                class="image"
                                src="<?php echo $item->imgSrc; ?>"
                                alt="<?php echo $item->alt; ?>"
                                title="<?php echo $item->caption; ?>"
                                width="<?php echo $params->get('thumbnailWidth')?>"
                                height="<?php echo $params->get('thumbnailHeight')?>"
                                />
                    <?php } else { echo $row->title; } ?>
                </a>
                <?php

            }     ?>
        </div>
        <?php
        $i++;
        if ($i == $params->get('matrixSize')) {
            ?>
            <div class="latest-postsclear"></div>
            <?php
            $i = 0;
        }
        ?>
        <?php endforeach; ?>

    <?php
    $comCedThumbnailsHelper = new comCedThumbnailsHelper();
    echo $comCedThumbnailsHelper->addFooter();
    ?>
</div>