<?php

namespace ToolboxBundle\Document\Areabrick\ParallaxContainer;

use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Asset;
use Symfony\Contracts\Translation\TranslatorInterface;
use Twig\Environment;

class ParallaxContainer extends AbstractAreabrick
{
    public function action(Editable\Area\Info $info)
    {
        parent::action($info);

        $config = $this->getConfigManager()->getAreaParameterConfig('parallaxContainer');

        /** @var Editable\Relation $parallaxBackgroundElement */
        $parallaxBackgroundElement = $this->getDocumentEditable($info->getDocument(), 'relation', 'background_image');
        $parallaxBackground = $parallaxBackgroundElement->getElement();
        $parallaxBackgroundColor = $this->getDocumentEditable($info->getDocument(), 'select', 'background_color')->getData();

        $parallaxTemplate = $this->getDocumentEditable($info->getDocument(), 'select', 'template')->getData();
        $parallaxBehind = $this->getDocumentEditable($info->getDocument(), 'parallaximage', 'image_behind');
        $parallaxFront = $this->getDocumentEditable($info->getDocument(), 'parallaximage', 'image_front');

        $backgroundMode = isset($config['background_mode']) ? $config['background_mode'] : 'wrap';
        $backgroundImageMode = isset($config['background_image_mode']) ? $config['background_image_mode'] : 'data';

        $backgroundTags = $this->getBackgroundTags($parallaxBackground, $parallaxBackgroundColor, $config, 'section');
        $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

        /** @var Environment $templating */
        $templating = $this->container->get('twig');
        /** @var TranslatorInterface $translator */
        $translator = $this->container->get('pimcore.translator');

        $behindElements = $parallaxBehind !== null
            ? $templating->render(
                $this->getTemplatePath('Partial/behind-front-elements'),
                ['elements' => $parallaxBehind, 'backgroundImageMode' => $backgroundImageMode, 'document' => $info->getDocument()]
            ) : null;

        $frontElements = $parallaxFront !== null
            ? $templating->render(
                $this->getTemplatePath('Partial/behind-front-elements'),
                ['elements' => $parallaxFront, 'backgroundImageMode' => $backgroundImageMode, 'document' => $info->getDocument()]
            ) : null;

        $info->setParams([
            'parallaxTemplate'     => $parallaxTemplate,
            'backgroundMode'       => $backgroundMode,
            'backgroundTags'       => $backgroundTags,
            'backgroundColorClass' => $backgroundColorClass,
            'behindElements'       => $behindElements,
            'frontElements'        => $frontElements,
            'sectionContent'       => $this->_buildSectionContent($info, $templating, $translator)
        ]);
    }

    private function _buildSectionContent(Editable\Area\Info $info, Environment $templating, TranslatorInterface $translator): string
    {
        ob_start();

        $config = $this->getConfigManager()->getAreaParameterConfig('parallaxContainerSection');

        /** @var Editable\Areablock $sectionBlock */
        $sectionBlock = $this->getDocumentEditable($info->getDocument(), 'block', 'pcB', ['default' => 1]);

        $loopIndex = 1;
        while ($sectionBlock->loop()) {
            $sectionConfig = '';

            /** @var Editable\Relation $parallaxBackgroundElement */
            $parallaxBackgroundElement = $this->getDocumentEditable($info->getDocument(), 'relation', 'background_image');
            $parallaxBackground = $parallaxBackgroundElement->getElement();
            $parallaxBackgroundColor = $this->getDocumentEditable($info->getDocument(), 'select', 'background_color')->getData();

            $backgroundTags = $this->getBackgroundTags($parallaxBackground, $parallaxBackgroundColor, $config, 'section');
            $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

            $template = $this->getDocumentEditable($info->getDocument(), 'select', 'template')->getData();
            $containerWrapper = $this->getDocumentEditable($info->getDocument(), 'select', 'container_type')->getData();

            $areaArgs = ['name' => 'pcs', 'type' => 'parallaxContainer', 'document' => $info->getDocument()];
            $areaBlock = $templating->render('@Toolbox/Helper/areablock.' . $this->getTemplateSuffix(), $areaArgs);

            if ($containerWrapper !== 'none') {
                $wrapperArgs = ['containerWrapperClass' => $containerWrapper, 'document' => $info->getDocument()];
                $wrapContent = $templating->render($this->getTemplatePath('Wrapper/container-wrapper'), $wrapperArgs);
                $areaBlock = sprintf($wrapContent, $areaBlock);
            }

            if ($info->getParam('editmode') === true) {
                $configNode = $this->getConfigManager()->getAreaConfig('parallaxContainerSection');
                $sectionConfig = $this->getBrickConfigBuilder()->buildElementConfig('parallaxContainerSection', 'Parallax Container Section', $info, $configNode);

                if ($containerWrapper === 'none' && strpos($areaBlock, 'toolbox-columns') !== false) {
                    $message = $translator->trans('You\'re using columns without a valid container wrapper.', [], 'admin');
                    $messageWrap = $templating->render('@Toolbox/Helper/field-alert.' . $this->getTemplateSuffix(), [
                        'type'     => 'danger',
                        'message'  => $message,
                        'document' => $info->getDocument()
                    ]);
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
            echo $templating->render($this->getTemplatePath('section'), $sectionArgs);
        }

        return ob_get_clean();
    }

    private function getBackgroundTags(Asset $backgroundImage, string $backgroundColor, array $config = [], string $type = 'parallax'): string
    {
        $backgroundImageMode = $config['background_image_mode'] ?? 'data';
        $backgroundColorMode = $config['background_color_mode'] ?? 'data';
        $thumbnail = $type === 'parallax'
            ? $this->configManager->getImageThumbnailFromConfig('parallax_background')
            : $this->configManager->getImageThumbnailFromConfig('parallax_section_background');

        $styles = [];
        $data = [];

        if ($backgroundImage instanceof Asset\Image) {
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
            $str .= implode(' ', array_map(function ($key) use ($styles) {
                return $key . ':' . $styles[$key] . ';';
            }, array_keys($styles)));
            $str .= '"';
        }

        if (count($data) > 0) {
            $str .= implode(' ', array_map(function ($key) use ($data) {
                return 'data-' . $key . '="' . $data[$key] . '"';
            }, array_keys($data)));
        }

        return $str;
    }

    private function getBackgroundColorClass(string $backgroundColor, array $config = [], string$type = 'parallax'): string
    {
        $mode = $config['background_color_mode'] ?? 'data';
        if ($backgroundColor === 'no-background-color' || empty($backgroundColor) || $mode !== 'class') {
            return '';
        }

        return $backgroundColor;
    }

    public function getName(): string
    {
        return 'Parallax Container';
    }

    public function getDescription(): string
    {
        return 'Toolbox Parallax Container';
    }
}
