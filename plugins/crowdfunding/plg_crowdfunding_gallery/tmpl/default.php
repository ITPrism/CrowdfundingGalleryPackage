<?php
/**
 * @package      CrowdfundingFiles
 * @subpackage   Plugins
 * @author       Todor Iliev
 * @copyright    Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license      GNU General Public License version 3 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Load the script that initializes the select element with banks.
$doc->addScript('plugins/crowdfunding/gallery/js/script.js?v=' . rawurlencode($this->version));
?>
<div class="panel panel-default">
    <div class="panel-heading">
        <h4><?php echo JText::_('PLG_CROWDFUNDING_GALLERY_GALLERY');?></h4>
    </div>

    <div class="panel-body">
        <span class="btn btn-primary fileinput-button">
            <span class="fa fa-upload"></span>
            <span><?php echo JText::_('PLG_CROWDFUNDING_GALLERY_UPLOAD');?></span>
            <!-- The file input field used as target for the file upload widget -->
            <input id="js-cfgallery-fileupload" type="file" name="media" data-url="<?php echo JRoute::_('index.php?option=com_magicgallery');?>" multiple />
        </span>
        <img src="media/com_crowdfunding/images/ajax-loader.gif" width="16" height="16" id="js-cfgallery-ajax-loader" style="display: none;" />

        <input type="hidden" name="project_id" value="<?php echo (int)$item->id; ?>" id="js-cfgallery-project-id" />
        <input type="hidden" name="catid" value="<?php echo (int)$this->params->get('category_id'); ?>" id="js-cfgallery-category-id" />

        <?php if ((string)$this->params->get('additional_information') !== '') { ?>
        <div class="bg-info p-5 mtb-10">
            <h5>
                <span class="fa fa-info-circle"></span>
                <?php echo JText::_('PLG_CROWDFUNDING_GALLERY_INFORMATION'); ?>
            </h5>
            <p><?php echo htmlentities($this->params->get('additional_information'), ENT_QUOTES, 'UTF-8'); ?></p>
        </div>
        <?php } ?>

        <div id="js-cfgallery-list">
        <?php foreach ($files as $file) {
            if (!$file->thumbnail) {
                continue;
            }

            $title = $file->title ? htmlentities($file->title, ENT_QUOTES, 'UTF-8') : '';
            ?>
        <div class="row mt-10" id="js-cfgallery-item<?php echo $file->id; ?>">
            <div class="col-md-3"><img src='<?php echo $mediaUrl .'/'. $file->thumbnail; ?>' alt="<?php echo $title; ?>"/></div>
            <div class="col-md-7"><?php echo $title; ?></div>
            <div class="col-md-2">
                <a class="btn btn-danger js-cfgallery-btn-remove" data-item-id="<?php echo (int)$file->id; ?>" href="javascript: void(0);">
                    <span class="fa fa-trash"></span>
                    <?php echo JText::_('PLG_CROWDFUNDING_GALLERY_DELETE');?>
                </a>
            </div>
        </div>
        <?php } ?>
        </div>

        <div class="row mt-10" style="display: none;" id="js-cfgallery-row-template">
            <div class="col-md-3" ><img src='' id="js-cfgallery-rt-img" /></div>
            <div class="col-md-7" id="js-cfgallery-rt-title"></div>
            <div class="col-md-2">
                <a class="btn btn-danger" id='js-cfgallery-rt-remove' href="javascript: void(0);">
                    <span class="fa fa-trash"></span>
                    <?php echo JText::_('PLG_CROWDFUNDING_GALLERY_DELETE');?>
                </a>
            </div>
        </div>

    </div>
</div>
