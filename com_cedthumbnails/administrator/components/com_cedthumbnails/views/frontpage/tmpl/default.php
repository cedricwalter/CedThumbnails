<?php
/**
 * @package     CedThumbnails
 * @subpackage  com_cedthumbnails
 *
 * @copyright   CedThumbnails 3.1.3 - Copyright (C) 2013-2017 galaxiis.com All rights reserved.
 * @license     The author and holder of the copyright of the software is Cédric Walter. The licensor and as such issuer of the license and bearer of the
 *              worldwide exclusive usage rights including the rights to reproduce, distribute and make the software available to the public
 *              in any form is Galaxiis.com
 *              see LICENSE.txt
 */

defined('_JEXEC') or die('Restricted access');

// Load the javascript
JHtml::_('behavior.framework');
JHtml::_('behavior.modal', 'a.modal');

$document = JFactory::getDocument();
$document->addStyleSheet("https://netdna.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.css");

?>

<h1>CedThumbnails 3.1.4 <?php echo $this->isFree; ?></h1>

<div>

    <div>
        <a href="https://www.galaxiis.com/" target="_blank" title="Home"><i class="fa fa-home fa-4x" aria-hidden="true">  Home.</i></a>
    </div>
    <div>
        <a href="https://www.galaxiis.com/cedthumbnails-doc/" target="_blank" title="Documentation"><i class="fa fa-book fa-4x" aria-hidden="true">  Documentation.</i></a>
    </div>
    <div>

        <a href="https://www.galaxiis.com/forums/" target="_blank" title="Forums"><i class="fa fa-life-ring fa-4x" aria-hidden="true">  Forums.</i></a>
    </div>
    <div>
        <a href="http://documentation.galaxiis.com/license" target="_blank" title="License"><i class="fa fa-gavel fa-4x" aria-hidden="true">  License.</i></a>
    </div>

    <div>
        <a href="https://www.galaxiis.com/cedthumbnails-jed/" target="_blank" title="Joomla JED review"><i class="fa fa-joomla fa-4x" aria-hidden="true"> Joomla JED review.</i></a>
    </div>
    <div>
        <div class="icon">
            <a href="https://www.galaxiis.com/cedthumbnails-download/" target="_blank" title="Download latest free version">
                <i class="fa fa-download fa-4x" aria-hidden="true">
                    Download latest free version.</i></a>
        </div>
    </div>
    <div>
        <a href="https://www.facebook.com/galaxiiscom" target="_blank" title="Like Us on Facebook"><i class="fa fa-facebook fa-4x" aria-hidden="true">
                Like Galaxiis on Facebook.</i></a>
    </div>
    <div>
        <a href="https://www.twitter.com/galaxiiscom" target="_blank" title="Follow Us on Twitter"><i class="fa fa-twitter fa-4x" aria-hidden="true">
                Follow Galaxiis on Twitter.</i></a>
    </div>
    <div>
        <a href="https://plus.google.com/u/0/104558366166000378462" target="_blank" title="Follow Me on Google+"><i class="fa fa-google-plus fa-4x" aria-hidden="true">
                Follow Galaxiis on Google+</i></a>
    </div>
</div>
<div></div>
<p>CedThumbnails 3.1.3 - Copyright (C) 2013-2017 galaxiis.com All rights reserved.</p>