<?php
/**
 * @package         Crowdfunding
 * @subpackage      Plugins
 * @author          Todor Iliev
 * @copyright       Copyright (C) 2016 Todor Iliev <todor@itprism.com>. All rights reserved.
 * @license         http://www.gnu.org/licenses/gpl-3.0.en.html GNU/GPL
 */

// no direct access
defined('_JEXEC') or die;

/**
 * Crowdfunding Gallery Plugin
 *
 * @package        Crowdfunding
 * @subpackage     Plugins
 */
class plgContentCrowdfundingGallery extends JPlugin
{
    protected $pluginUri;
    protected $mediaUrl;

    /**
     * @param string                     $context
     * @param   stdClass                 $item
     * @param   Joomla\Registry\Registry $params
     * @param int                        $page
     *
     * @return null|string
     */
    public function onContentAfterDisplay($context, &$item, &$params, $page = 0)
    {
        $app = JFactory::getApplication();
        /** @var $app JApplicationSite */

        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        if ($app->isAdmin() or (strcmp('html', $doc->getType()) !== 0)) {
            return null;
        }

        if ((strcmp('com_crowdfunding.details', $context) !== 0) or !JComponentHelper::isInstalled('com_magicgallery')) {
            return null;
        }

        // Load language
        $this->loadLanguage();

        // Import the libraries.
        jimport('Prism.init');
        jimport('Magicgallery.init');

        $keys = array(
            'object_id' => $item->id,
            'user_id'   => $item->user_id,
            'extension' => 'com_crowdfunding'
        );

        $options = array(
            'load_resources' => true,
            'resource_state' => Prism\Constants::PUBLISHED
        );

        $gallery = new Magicgallery\Gallery\Gallery(JFactory::getDbo());
        $gallery->load($keys, $options);
        if (!$gallery->getId()) {
            return '';
        }

        $resources = $gallery->getEntities();

        // If there are no images, return empty string.
        if (count($resources) === 0) {
            return '';
        }

        $this->pluginUri = JUri::root() . 'plugins/content/crowdfundingimages';

        // Prepare the parameters of Magic Gallery.
        $componentParams = JComponentHelper::getParams('com_magicgallery');
        /** @var  $componentParams Joomla\Registry\Registry */

        $filesystemHelper = new Prism\Filesystem\Helper($componentParams);
        $pathHelper       = new Magicgallery\Helper\Path($filesystemHelper);

        // Prepare media URI.
        $this->mediaUrl = $pathHelper->getMediaUri($gallery);

        $html = array();

        $html[] = '<div class="panel panel-default">';

        // Display title
        if ($this->params->get('display_title', 0)) {
            $html[] = '<div class="panel-heading"><h4>' . JText::_('PLG_CONTENT_CROWDFUNDINGGALLERY_GALLERY') . '</h4></div>';
        }

        $html[] = '<div class="panel-body">';

        // Load jQuery library
        if ($this->params->get('include_jquery', 0)) {
            JHtml::_('jquery.framework');
        }

        switch ($this->params->get('gallery')) {
            case 'magnific':
                $html = $this->prepareMagnific($resources, $html);
                break;

            case 'nivo':
                $html = $this->prepareNivo($resources, $html, $item->id);
                break;

            default: // FancyBox
                $html = $this->prepareFancybox($resources, $html, $item->id);
                break;
        }

        $html[] = '</div>';
        $html[] = '</div>';

        return implode("\n", $html);
    }

    private function prepareMagnific($resources, $html)
    {
        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        JHtml::_('MagicGallery.lightboxMagnific');

        $js = '
        jQuery(document).ready(function() {
        	jQuery("#js-extra-images-gallery").magnificPopup({
        		delegate: "a",
        		type: "image",
        		mainClass: "mfp-img-mobile",
        		gallery: {
        			enabled: true,
        			navigateByImgClick: true,
        			preload: [0,1], // Will preload 0 - before current, and 1 after the current image
                    arrowMarkup: \'<button type="button" class="mfp-arrow mfp-arrow-%dir%"></button>\',
        		}
        	});
        });
        ';

        $doc->addScriptDeclaration($js);

        $html[] = '<div id="js-extra-images-gallery">';

        /** @var array $resources */
        /** @var stdClass $resource */
        foreach ($resources as $resource) {
            $html[] = '<a href="' . $this->mediaUrl . '/' . $resource->image . '">';
            $html[] = '<img src="' . $this->mediaUrl . '/' . $resource->thumbnail . '" />';
            $html[] = '</a>';
        }

        $html[] = '</div>';

        return $html;
    }

    private function prepareFancybox($resources, $html, $projectId)
    {
        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        JHtml::_('MagicGallery.lightboxFancybox');

        $js = '
        jQuery(document).ready(function() {
                
            jQuery("a.js-extra-images-gallery").fancybox({
        		"transitionIn"	:	"fade",
        		"transitionOut"	:	"fade",
        		"speedIn"		:	600, 
        		"speedOut"		:	200, 
        		"overlayShow"	:	true
        	});
                
        });
        ';

        $doc->addScriptDeclaration($js);

        /** @var array $resources */
        /** @var stdClass $resource */
        foreach ($resources as $resource) {
            $html[] = '<a class="js-extra-images-gallery" rel="eigroup' . (int)$projectId . '" href="' . $this->mediaUrl . '/' . $resource->image . '">';
            $html[] = '<img src="' . $this->mediaUrl . '/' . $resource->thumbnail . '" />';
            $html[] = '</a>';
        }

        return $html;
    }

    private function prepareNivo($resources, $html, $projectId)
    {
        $doc = JFactory::getDocument();
        /**  @var $doc JDocumentHtml */

        JHtml::_('MagicGallery.lightboxNivo');

        $js = '
        jQuery(document).ready(function() {
            jQuery("a.js-extra-images-gallery").nivoLightbox({
        		effect: "fade",                             // The effect to use when showing the lightbox
                theme: "default",                             // The lightbox theme to use
                keyboardNav: true,                             // Enable/Disable keyboard navigation (left/right/escape)
                clickOverlayToClose: true,
        	});
        });
        ';

        $doc->addScriptDeclaration($js);

        /** @var array $resources */
        /** @var stdClass $resource */
        foreach ($resources as $resource) {
            $html[] = '<a class="js-extra-images-gallery" data-lightbox-gallery="cfgallery' . (int)$projectId . '" href="' . $this->mediaUrl . '/' . $resource->image . '">';
            $html[] = '<img src="' . $this->mediaUrl . '/' . $resource->thumbnail . '" />';
            $html[] = '</a>';
        }

        return $html;
    }
}
