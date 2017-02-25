<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;
use Toolbox\Config;

class ParallaxContainer extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $config = Config::getConfig()->parallaxContainer->toArray();

        $parallaxTemplate = $this->getView()->select('template')->getData();
        $parallaxBackground = $this->getView()->href('background');
        $parallaxBehind = $this->getView()->parallaximage('imagesBehind');
        $parallaxFront = $this->getView()->parallaximage('imageFront');

        $backgroundMode = isset($config['backgroundMode']) ? $config['backgroundMode'] : 'wrap';
        $backgroundImageMode = isset($config['backgroundImageMode']) ? $config['backgroundImageMode'] : 'data';

        $imageUrl = $parallaxBackground->getElement() instanceOf \Pimcore\Model\Asset
            ? $parallaxBackground->getElement()->getThumbnail('parallaxBackground')
            : '';

        $backgroundImageTag = $backgroundImageMode === 'style'
            ? 'style="background-image:url(' . $imageUrl . ');"'
            : 'data-background-image="' . $imageUrl . '"';

        $behindElements = !empty($parallaxBehind)
            ? $this->getView()->partial(
                'toolbox/parallaxContainer/partial/behind-front-elements.php',
                ['elements' => $parallaxBehind, 'backgroundImageMode' => $backgroundImageMode]
            ) : NULL;

        $frontElements = !empty($parallaxFront)
            ? $this->getView()->partial(
                'toolbox/parallaxContainer/partial/behind-front-elements.php',
                ['elements' => $parallaxFront, 'backgroundImageMode' => $backgroundImageMode]
            ) : NULL;

        $this->getView()->assign([
            'parallaxTemplate'   => $parallaxTemplate,
            'backgroundMode'     => $backgroundMode,
            'backgroundImageTag' => $backgroundImageTag,
            'behindElements'     => $behindElements,
            'frontElements'      => $frontElements,
            'sectionContent'     => $this->_buildSectionContent(),

        ]);
    }

    private function _buildSectionContent()
    {
        ob_start();

        $sectionBlock = $this->getView()->block('pcB', ['default' => 1]);

        $loopIndex = 1;
        while ($sectionBlock->loop()) {

            $sectionConfig = '';

            $template = $this->getView()->select('template')->getData();
            $containerWrapper = $this->getView()->select('containerType')->getData();

            $areaArgs = ['name' => 'pcs', 'type' => 'parallaxContainer'];
            $areaBlock = $this->getView()->template('helper/areablock.php', $areaArgs, FALSE, 'toolbox_capture_areablock');

            if ($containerWrapper !== 'none') {
                $wrapperArgs = ['containerWrapperClass' => $containerWrapper];
                $wrapContent = $this->getView()->partial('toolbox/parallaxContainer/wrapper/container-wrapper.php', $wrapperArgs);
                $areaBlock = sprintf($wrapContent, $areaBlock);
            }

            if ($this->getView()->editmode) {

                $sectionConfig = \Toolbox\Tool\ElementBuilder::buildElementConfig('parallaxContainerSection', $this->getView());
                if ($containerWrapper === 'none' && strpos($areaBlock, 'toolbox-columns') !== FALSE) {
                    $message = $this->getView()->translateAdmin('You\'re using columns without a valid container wrapper.');
                    $messageWrap = $this->getView()->partial('helper/field-alert.php', ['type' => 'danger', 'message' => $message]);
                    $areaBlock = $messageWrap . $areaBlock;
                }
            }

            $sectionArgs = [
                'content'      => $areaBlock,
                'template'     => $template,
                'loopIndex'    => $loopIndex,
                'sectionIndex' => $sectionBlock->getCurrentIndex()
            ];

            $loopIndex++;

            echo $sectionConfig;
            echo $this->getView()->template('toolbox/parallaxContainer/section.php', $sectionArgs, FALSE, 'toolbox_capture_section');
        }

        $string = ob_get_clean();

        return $string;
    }

    public function getBrickHtmlTagOpen($brick)
    {
        return '';
    }

    public function getBrickHtmlTagClose($brick)
    {
        return '';
    }
}