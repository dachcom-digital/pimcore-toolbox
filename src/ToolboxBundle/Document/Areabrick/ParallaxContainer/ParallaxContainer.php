<?php

namespace ToolboxBundle\Document\Areabrick\ParallaxContainer;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class ParallaxContainer extends AbstractAreabrick
{
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        $config = $this->getConfigManager()->getAreaParameterConfig('parallaxContainer');

        $parallaxBackground = $this->getDocumentTag($info->getDocument(), 'href', 'backgroundImage')->getELement();
        $parallaxBackgroundColor = $this->getDocumentTag($info->getDocument(), 'select', 'backgroundColor')->getData();

        $parallaxTemplate = $this->getDocumentTag($info->getDocument(), 'select', 'template')->getData();
        $parallaxBehind = $this->getDocumentTag($info->getDocument(), 'parallaximage', 'imagesBehind');
        $parallaxFront = $this->getDocumentTag($info->getDocument(), 'parallaximage', 'imageFront');

        $backgroundMode = isset($config['backgroundMode']) ? $config['backgroundMode'] : 'wrap';
        $backgroundImageMode = isset($config['backgroundImageMode']) ? $config['backgroundImageMode'] : 'data';

        $backgroundTags = $this->getBackgroundTags($parallaxBackground, $parallaxBackgroundColor, $config, 'section');
        $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

        $templating = $this->container->get('templating');
        $translator = $this->container->get('pimcore.translator');

        $behindElements = !empty($parallaxBehind)
            ? $templating->render(
                '@Toolbox/Toolbox/ParallaxContainer/Partial/behind-front-elements.' . $this->getTemplateSuffix(),
                ['elements' => $parallaxBehind, 'backgroundImageMode' => $backgroundImageMode, 'document' => $info->getDocument()]
            ) : NULL;

        $frontElements = !empty($parallaxFront)
            ? $templating->render(
                '@Toolbox/Toolbox/ParallaxContainer/Partial/behind-front-elements.' . $this->getTemplateSuffix(),
                ['elements' => $parallaxFront, 'backgroundImageMode' => $backgroundImageMode, 'document' => $info->getDocument()]
            ) : NULL;

        $view->parallaxTemplate = $parallaxTemplate;
        $view->backgroundMode = $backgroundMode;
        $view->backgroundTags = $backgroundTags;
        $view->backgroundColorClass = $backgroundColorClass;
        $view->behindElements = $behindElements;
        $view->frontElements = $frontElements;
        $view->sectionContent = $this->_buildSectionContent($info, $templating, $translator);

    }

    /**
     * @param Info $info
     *
     * @return string
     */
    private function _buildSectionContent(Info $info, $templating, $translator)
    {
        ob_start();

        $config = $this->getConfigManager()->getAreaParameterConfig('parallaxContainerSection');

        $sectionBlock = $this->getDocumentTag($info->getDocument(), 'block', 'pcB', ['default' => 1]);

        $loopIndex = 1;
        while ($sectionBlock->loop()) {

            $sectionConfig = '';

            $parallaxBackground = $this->getDocumentTag($info->getDocument(), 'href', 'backgroundImage')->getElement();
            $parallaxBackgroundColor = $this->getDocumentTag($info->getDocument(), 'select', 'backgroundColor')->getData();

            $backgroundTags = $this->getBackgroundTags($parallaxBackground, $parallaxBackgroundColor, $config, 'section');
            $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

            $template = $this->getDocumentTag($info->getDocument(), 'select', 'template')->getData();
            $containerWrapper = $this->getDocumentTag($info->getDocument(), 'select', 'containerType')->getData();

            $areaArgs = ['name' => 'pcs', 'type' => 'parallaxContainer', 'document' => $info->getDocument()];
            $areaBlock = $templating->render('@Toolbox/Helper/areablock.' . $this->getTemplateSuffix(), $areaArgs);

            if ($containerWrapper !== 'none') {
                $wrapperArgs = ['containerWrapperClass' => $containerWrapper, 'document' => $info->getDocument()];
                $wrapContent = $templating->render('@Toolbox/Toolbox/ParallaxContainer/wrapper/container-wrapper.' . $this->getTemplateSuffix(), $wrapperArgs);
                $areaBlock = sprintf($wrapContent, $areaBlock);
            }

            if ($info->getView()->get('editmode') === TRUE) {

                $sectionConfig = $this->getElementBuilder()->buildElementConfig('parallaxContainerSection', 'Parallax Container Section', $info);
                if ($containerWrapper === 'none' && strpos($areaBlock, 'toolbox-columns') !== FALSE) {
                    $message = $translator->trans('You\'re using columns without a valid container wrapper.', [], 'admin');
                    $messageWrap = $templating->render('@Toolbox/Helper/field-alert.' . $this->getTemplateSuffix(), ['type' => 'danger', 'message' => $message, 'document' => $info->getDocument()]);
                    $areaBlock = $messageWrap . $areaBlock;
                }
            }

            $sectionArgs = [

                'backgroundTags'       => $backgroundTags,
                'backgroundColorClass' => $backgroundColorClass,
                'content'              => $areaBlock,
                'template'             => $template,
                'loopIndex'            => $loopIndex,
                'sectionIndex'         => $sectionBlock->getCurrentIndex(),
                'document'             => $info->getDocument()
            ];

            $loopIndex++;

            echo $sectionConfig;
            echo $templating->render('@Toolbox/Toolbox/ParallaxContainer/section.' . $this->getTemplateSuffix(), $sectionArgs);
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
            $image = $backgroundImage->getThumbnail($thumbnail);
            if ($backgroundImageMode === 'style') {
                $styles['background-image'] = 'url(\'' . $image . '\')';
            } else {
                $data['background-image'] = $image;
            }
        }

        if ($backgroundColor !== 'no-background-color' && !empty($backgroundColor) && $backgroundColorMode !== 'class') {
            if ($backgroundColorMode === 'style') {
                $styles['background-color'] = $backgroundColor;
            } else {
                $data['background-color'] = $backgroundColor;
            }
        }

        $str = '';

        if (count($styles) > 0) {
            $str .= 'style="';
            $str .= join(' ', array_map(function ($key) use ($styles) {
                return $key . ':' . $styles[$key] . ';';
            }, array_keys($styles)));
            $str .= '"';
        }

        if (count($data) > 0) {
            $str .= join(' ', array_map(function ($key) use ($data) {
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

    public function getViewTemplate()
    {
        return 'ToolboxBundle:Areas/ParallaxContainer:view.' . $this->getTemplateSuffix();
    }

    public function getName()
    {
        return 'Parallax Container';
    }

    public function getDescription()
    {
        return 'Toolbox Parallax Container';
    }
}