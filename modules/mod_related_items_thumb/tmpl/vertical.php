<?php
/**
 * @package     cedThumbnails
 * @subpackage  mod_related_items_thumb
 *
 * @copyright   Copyright (C) 2013-2016 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="related-vert-posts<?php echo $moduleclass_sfx; ?>">
    <ul>
        <?php foreach ($list as $item) : ?>
            <li>
                <div class="item-content">
                    <?php if ($params->get('useThumbnails')) { ?>
                        <div class="item-thumbnail">
                            <a href="<?php echo $item->route; ?>" target="_blank">
                                <img class="hasTip"
                                     src="<?php echo $item->imgSrc; ?>"
                                     alt="<?php echo $item->alt; ?>"
                                     title="<?php echo $item->caption; ?>"
                                     width="<?php echo $params->get('thumbnailWidth') ?>"
                                     height="<?php echo $params->get('thumbnailHeight') ?>"
                                    />
                            </a>
                        </div>
                    <?php } ?>
                    <?php if ($params->get('useTitle')) { ?>
                        <div class="item-title"><a href="<?php echo $item->route; ?>"><?php echo $item->title; ?></a></div>
                    <?php } ?>
                    <?php if ($params->get('useTeaser')) { ?>
                        <div class="item-snippet"><?php echo $item->teaser; ?> <?php echo $params->get('teaserEnding'); ?></div>
                    <?php } ?>
                </div>
                <div style="clear: both;"></div>
            </li>
        <?php endforeach; ?>
    </ul>

    <?php
    $comCedThumbnailsHelper = new comCedThumbnailsHelper();
    echo $comCedThumbnailsHelper->addFooter();
    ?>
</div>
