<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Document\ToolboxAreabrick\ParallaxContainer;

use Pimcore\Model\Asset;
use Pimcore\Model\Document\Editable;
use Pimcore\Model\Element\ElementInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Templating\EngineInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;

class ParallaxContainer extends AbstractAreabrick
{
    public function __construct(
        private TranslatorInterface $translator,
        private EngineInterface $templating
    ) {
    }

    public function action(Editable\Area\Info $info): ?Response
    {
        parent::action($info);

        $config = $this->getConfigManager()->getAreaParameterConfig('parallaxContainer');

        /** @var Editable\Relation $parallaxBackgroundElement */
        $parallaxBackgroundElement = $this->getDocumentEditable($info->getDocument(), 'relation', 'background_image');
        $parallaxBackgroundColor = $this->getDocumentEditable($info->getDocument(), 'select', 'background_color')->getData();

        $parallaxTemplate = $this->getDocumentEditable($info->getDocument(), 'select', 'template')->getData();
        $parallaxBehind = $this->getDocumentEditable($info->getDocument(), 'parallaximage', 'image_behind');
        $parallaxFront = $this->getDocumentEditable($info->getDocument(), 'parallaximage', 'image_front');

        $backgroundMode = $config['background_mode'] ?? 'wrap';
        $backgroundImageMode = $config['background_image_mode'] ?? 'data';

        $backgroundTags = $this->getBackgroundTags($parallaxBackgroundElement->getElement(), $parallaxBackgroundColor, $config, 'section');
        $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

        $behindElements = !$parallaxFront->isEmpty()
            ? $this->templating->render(
                $this->getTemplatePath('partial/behind_front_elements'),
                ['elements' => $parallaxBehind, 'backgroundImageMode' => $backgroundImageMode, 'document' => $info->getDocument()]
            ) : null;

        $frontElements = !$parallaxFront->isEmpty()
            ? $this->templating->render(
                $this->getTemplatePath('partial/behind_front_elements'),
                ['elements' => $parallaxFront, 'backgroundImageMode' => $backgroundImageMode, 'document' => $info->getDocument()]
            ) : null;

        $info->setParams(array_merge($info->getParams(), [
            'parallaxTemplate'     => $parallaxTemplate,
            'backgroundMode'       => $backgroundMode,
            'backgroundTags'       => $backgroundTags,
            'backgroundColorClass' => $backgroundColorClass,
            'behindElements'       => $behindElements,
            'frontElements'        => $frontElements,
            'sectionContent'       => $this->buildSectionContent($info)
        ]));

        return null;
    }

    /**
     * @throws \Exception
     */
    private function buildSectionContent(Editable\Area\Info $info): string
    {
        ob_start();

        $config = $this->getConfigManager()->getAreaParameterConfig('parallaxContainerSection');

        /** @var Editable\Areablock $sectionBlock */
        $sectionBlock = $this->getDocumentEditable($info->getDocument(), 'block', 'pcB', ['default' => 1]);

        $loopIndex = 1;
        while ($sectionBlock->loop()) {
            /** @var Editable\Relation $parallaxBackgroundElement */
            $parallaxBackgroundElement = $this->getDocumentEditable($info->getDocument(), 'relation', 'background_image');
            $parallaxBackgroundColor = $this->getDocumentEditable($info->getDocument(), 'select', 'background_color')->getData();

            $backgroundTags = $this->getBackgroundTags($parallaxBackgroundElement->getElement(), $parallaxBackgroundColor, $config, 'section');
            $backgroundColorClass = $this->getBackgroundColorClass($parallaxBackgroundColor, $config, 'section');

            $template = $this->getDocumentEditable($info->getDocument(), 'select', 'template')->getData();
            $containerWrapper = $this->getDocumentEditable($info->getDocument(), 'select', 'container_type')->getData();

            $areaArgs = ['name' => 'pcs', 'type' => 'parallaxContainer', 'document' => $info->getDocument()];
            $areaBlock = $this->templating->render('@Toolbox/helper/areablock.' . $this->getTemplateSuffix(), $areaArgs);

            if ($containerWrapper !== 'none') {
                $wrapperArgs = ['containerWrapperClass' => $containerWrapper, 'document' => $info->getDocument()];
                $wrapContent = $this->templating->render($this->getTemplatePath('wrapper/container_wrapper'), $wrapperArgs);
                $areaBlock = sprintf($wrapContent, $areaBlock);
            }

            if ($info->getEditable()?->getEditmode() === true) {
                if ($containerWrapper === 'none' && str_contains($areaBlock, 'toolbox-columns')) {
                    $message = $this->translator->trans('You\'re using columns without a valid container wrapper.', [], 'admin');
                    $messageWrap = $this->templating->render('@Toolbox/helper/field-alert.' . $this->getTemplateSuffix(), [
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

            echo $this->templating->render($this->getTemplatePath('section'), $sectionArgs);
        }

        return ob_get_clean();
    }

    /**
     * @throws \Exception
     */
    private function getBackgroundTags(?ElementInterface $backgroundImage, ?string $backgroundColor, array $config = [], string $type = 'parallax'): string
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
            $str .= implode(' ', array_map(static function ($key) use ($styles) {
                return $key . ':' . $styles[$key] . ';';
            }, array_keys($styles)));
            $str .= '"';
        }

        if (count($data) > 0) {
            $str .= implode(' ', array_map(static function ($key) use ($data) {
                return 'data-' . $key . '="' . $data[$key] . '"';
            }, array_keys($data)));
        }

        return $str;
    }

    private function getBackgroundColorClass(?string $backgroundColor, array $config = [], string $type = 'parallax'): string
    {
        $mode = $config['background_color_mode'] ?? 'data';
        if ($backgroundColor === 'no-background-color' || empty($backgroundColor) || $mode !== 'class') {
            return '';
        }

        return $backgroundColor;
    }

    public function getTemplateDirectoryName(): string
    {
        return 'parallax_container';
    }

    public function getTemplate(): string
    {
        return sprintf('@Toolbox/areas/%s/view.%s', $this->getTemplateDirectoryName(), $this->getTemplateSuffix());
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
