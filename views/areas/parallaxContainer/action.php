<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;
use Toolbox\Config;

class ParallaxContainer extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $config = Config::getConfig()->parallaxContainer->toArray();

        $containerWrapper = $this->getView()->select('containerType')->getData();
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

        $areaBlock = $this->getView()->template('helper/areablock.php', ['name' => 'p-container-block', 'type' => 'parallaxContainer'], FALSE, TRUE);

        if ($this->getView()->editmode) {
            if ($containerWrapper === 'none' && strpos($areaBlock, 'toolbox-columns') !== FALSE) {
                $message = $this->getView()->translateAdmin('You\'re using columns without a valid container wrapper.');
                $messageWrap = '<div class="alert alert-danger">' . $message . '</div>';
                $areaBlock = $messageWrap . $areaBlock;
            }
        }

        $wrapContent = '%s';
        if ($containerWrapper !== 'none') {
            $wrapContent = $this->getView()->partial('toolbox/parallaxContainer/template/container-wrapper.php', ['containerWrapperClass' => $containerWrapper]);
        }

        $content = sprintf($wrapContent, $areaBlock);

        $this->getView()->assign([
            'content'            => $content,
            'backgroundMode'     => $backgroundMode,
            'backgroundImageTag' => $backgroundImageTag,
            'behindElements'     => $behindElements,
            'frontElements'      => $frontElements
        ]);
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