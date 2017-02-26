<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;
use Toolbox\Config;
use Toolbox\Tool;

class ParallaxContainer extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $_config = Config::getConfig()->parallaxContainer;
        $config = [];

        if ($_config instanceof \Zend_Config) {
            $config = $_config->toArray();
        }

        $parallaxBackground = $this->getView()->href('backgroundImage')->getELement();
        $parallaxBackgroundColor = $this->getView()->select('backgroundColor')->getData();

        $parallaxTemplate = $this->getView()->select('template')->getData();
        $parallaxBehind = $this->getView()->parallaximage('imagesBehind');
        $parallaxFront = $this->getView()->parallaximage('imageFront');

        $backgroundMode = isset($config['backgroundMode']) ? $config['backgroundMode'] : 'wrap';
        $backgroundImageMode = isset($config['backgroundImageMode']) ? $config['backgroundImageMode'] : 'data';

        $backgroundTags = $this->getBackgroundTags($parallaxBackground, $parallaxBackgroundColor, $config, 'section');
        $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

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
            'parallaxTemplate'     => $parallaxTemplate,
            'backgroundMode'       => $backgroundMode,
            'backgroundTags'       => $backgroundTags,
            'backgroundColorClass' => $backgroundColorClass,
            'behindElements'       => $behindElements,
            'frontElements'        => $frontElements,
            'sectionContent'       => $this->_buildSectionContent(),

        ]);
    }

    private function _buildSectionContent()
    {
        ob_start();

        $_config = Config::getConfig()->parallaxContainerSection;
        $config = [];

        if ($_config instanceof \Zend_Config) {
            $config = $_config->toArray();
        }

        $sectionBlock = $this->getView()->block('pcB', ['default' => 1]);

        $loopIndex = 1;
        while ($sectionBlock->loop()) {

            $sectionConfig = '';

            $parallaxBackground = $this->getView()->href('backgroundImage')->getElement();
            $parallaxBackgroundColor = $this->getView()->select('backgroundColor')->getData();

            $backgroundTags = $this->getBackgroundTags($parallaxBackground, $parallaxBackgroundColor, $config, 'section');
            $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

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

                $sectionConfig = Tool\ElementBuilder::buildElementConfig('parallaxContainerSection', $this->getView());
                if ($containerWrapper === 'none' && strpos($areaBlock, 'toolbox-columns') !== FALSE) {
                    $message = $this->getView()->translateAdmin('You\'re using columns without a valid container wrapper.');
                    $messageWrap = $this->getView()->partial('helper/field-alert.php', ['type' => 'danger', 'message' => $message]);
                    $areaBlock = $messageWrap . $areaBlock;
                }
            }

            $sectionArgs = [

                'backgroundTags'       => $backgroundTags,
                'backgroundColorClass' => $backgroundColorClass,
                'content'              => $areaBlock,
                'template'             => $template,
                'loopIndex'            => $loopIndex,
                'sectionIndex'         => $sectionBlock->getCurrentIndex()
            ];

            $loopIndex++;

            echo $sectionConfig;
            echo $this->getView()->template('toolbox/parallaxContainer/section.php', $sectionArgs, FALSE, 'toolbox_capture_section');
        }

        $string = ob_get_clean();

        return $string;
    }

    private function getBackgroundTags($backgroundImage, $backgroundColor, $config = [], $type = 'parallax')
    {
        $backgroundImageMode = isset($config['backgroundImageMode']) ? $config['backgroundImageMode'] : 'data';
        $backgroundColorMode = isset($config['backgroundColorMode']) ? $config['backgroundColorMode'] : 'data';
        $thumbnail = $type === 'parallax' ? 'parallaxBackground' : 'parallaxSectionBackground';

        $styles = [];
        $data = [];

        if ($backgroundImage instanceOf \Pimcore\Model\Asset) {
            $image =  $backgroundImage->getThumbnail($thumbnail);
            if($backgroundImageMode === 'style') {
                $styles['background-image'] = 'url(\'' . $image . '\')';
            } else {
                $data['background-image'] = $image;
            }
        }

        if ($backgroundColor !== 'no-background-color' && !empty($backgroundColor) && $backgroundColorMode !== 'class') {
            if($backgroundColorMode === 'style') {
                $styles['background-color'] = $backgroundColor;
            } else {
                $data['background-color'] = $backgroundColor;
            }
        }

        $str = '';

        if(count($styles) > 0) {
            $str .= 'style="';
            $str .= join(' ', array_map(function($key) use ($styles) {
                return $key . ':' . $styles[$key] . ';';
            }, array_keys($styles)));
            $str .= '"';
        }

        if(count($data) > 0) {
            $str .= join(' ', array_map(function($key) use ($data) {
                return 'data-' . $key . '="' . $data[$key] . '"';
            }, array_keys($data)));
        }

        return $str;

    }

    private function getBackgroundColorClass($backgroundColor, $config = [], $type = 'parallax')
    {
        $mode = isset($config['backgroundColorMode']) ? $config['backgroundColorMode'] : 'data';

        if ($backgroundColor === 'no-background-color' || empty($backgroundColor) || $mode !== 'class') {
            return '';
        }

        return $backgroundColor;
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