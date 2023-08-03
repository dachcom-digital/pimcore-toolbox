<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractAreabrick as PimcoreAbstractAreabrick;
use Pimcore\Model\Document;
use Pimcore\Model\Document\PageSnippet;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;
use ToolboxBundle\Builder\InlineConfigBuilderInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;
use ToolboxBundle\Event\HeadlessEditableActionEvent;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManagerInterface;
use ToolboxBundle\ToolboxEvents;

abstract class AbstractBaseAreabrick extends PimcoreAbstractAreabrick
{
    public const AREABRICK_TYPE_INTERNAL = 'internal';
    public const AREABRICK_TYPE_EXTERNAL = 'external';

    protected ConfigManagerInterface $configManager;
    protected LayoutManagerInterface $layoutManager;
    protected InlineConfigBuilderInterface $inlineConfigBuilder;
    protected EventDispatcherInterface $eventDispatcher;

    protected string $areaBrickType = 'internal';
    protected ?array $areaConfig = null;
    protected ?array $areaThemeConfig = null;
    protected ?array $areaThemeOptions = null;

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        $info->setParams(
            array_merge(
                $info->getParams(),
                [
                    'areaId'                => $this->getId(),
                    'areaTemplateDirectory' => $this->getTemplateDirectoryName(),
                    'additionalClassesData' => $this->configureAdditionalClasses($info),
                    'elementThemeConfig'    => $this->getAreaThemeConfig(),
                ]
            )
        );

        $this->checkInlineConfigElements($info);

        return null;
    }

    public function headlessAction(Document\Editable\Area\Info $info, HeadlessResponse $headlessResponse): void
    {
        $configNode = $this->getAreaConfig();
        $themeOptions = $this->getAreaThemeOptions();

        $headlessResponse->setBrickConfiguration([
            'areaId'                => $this->getId(),
            'additionalClassesData' => $this->configureAdditionalClasses($info),
        ]);

        $headlessResponse->setInlineConfigElementData(
            $this->inlineConfigBuilder->buildInlineConfigurationData(
                $info,
                $this->getId(),
                $configNode,
                $themeOptions
            )
        );

        if (!$this instanceof AbstractAreabrick) {
            $this->triggerHeadlessEditableActionEvent($info, $headlessResponse);
        }

    }

    public function getTemplateDirectoryName(): string
    {
        return $this->getId();
    }

    public function getTemplatePath(string $viewName = 'view'): string
    {
        return $this->layoutManager->getAreaTemplatePath($this->getId(), $this->getTemplateDirectoryName(), $viewName);
    }

    public function getTemplateLocation(): string
    {
        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL) {
            return static::TEMPLATE_LOCATION_BUNDLE;
        }

        return static::TEMPLATE_LOCATION_GLOBAL;
    }

    public function getTemplate(): ?string
    {
        return null;
    }

    public function getTemplateSuffix(): string
    {
        return static::TEMPLATE_SUFFIX_TWIG;
    }

    public function getIcon(): ?string
    {
        if ($this->getAreaBrickType() === self::AREABRICK_TYPE_EXTERNAL) {
            return null;
        }

        return '/bundles/toolbox/areas/' . $this->getTemplateDirectoryName() . '/icon.svg';
    }

    public function getHtmlTagOpen(Document\Editable\Area\Info $info): string
    {
        return '';
    }

    public function getHtmlTagClose(Document\Editable\Area\Info $info): string
    {
        return '';
    }

    public function isHeadlessLayoutAware(): bool
    {
        $themeConfig = $this->getAreaThemeConfig();

        return $themeConfig['layout'] === LayoutManagerInterface::TOOLBOX_LAYOUT_HEADLESS;
    }


    private function checkInlineConfigElements(Document\Editable\Area\Info $info): void
    {
        $areaConfig = $this->getAreaConfig();
        $areaThemeOptions = $this->getAreaThemeOptions();

        if ($this->isHeadlessLayoutAware() === false) {
            return;
        }

        if ($info->getEditable()?->getEditmode() === false) {
            return;
        }

        $info->setParam(
            'inlineConfigElements',
            $this->inlineConfigBuilder->buildInlineConfiguration(
                $info,
                $this->getId(),
                $areaConfig,
                $areaThemeOptions,
                true
            )
        );
    }

    private function configureAdditionalClasses(Document\Editable\Area\Info $info): array
    {
        $classesArray = [];
        $areaConfig = $this->getAreaConfig();

        if (!isset($areaConfig['config_elements'])) {
            return $classesArray;
        }

        foreach ($areaConfig['config_elements'] as $name => $configElement) {
            if (!isset($configElement['type'])) {
                continue;
            }

            if ($configElement['type'] === 'additionalClasses') {
                $addClassField = $this->getDocumentEditable($info->getDocument(), 'select', 'add_classes');
                if ($addClassField instanceof Document\Editable\Select && !empty($addClassField->getValue())) {
                    $classesArray[] = (string) $addClassField->getValue();
                }
            } elseif ($configElement['type'] === 'additionalClassesChained') {
                $chainedElementName = explode('_', $name);
                $chainedIncrementor = end($chainedElementName);
                $addChainedClassField = $this->getDocumentEditable($info->getDocument(), 'select', 'add_cclasses_' . $chainedIncrementor);
                if ($addChainedClassField instanceof Document\Editable\Select && !empty($addChainedClassField->getValue())) {
                    $classesArray[] = (string) $addChainedClassField->getValue();
                }
            }
        }

        return $classesArray;
    }

    protected function triggerHeadlessEditableActionEvent(Document\Editable\Area\Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->eventDispatcher->dispatch(
            new HeadlessEditableActionEvent($info, $headlessResponse, function (PageSnippet $document, string $type, string $inputName, array $options = []) {
                return $this->getDocumentEditable($document, $type, $inputName, $options);
            }),
            ToolboxEvents::HEADLESS_EDITABLE_ACTION
        );
    }

    protected function getAreaConfigNode(string $node, string $configProperty): mixed
    {
        $config = $this->getAreaConfig();

        if (isset($config['config_elements'][$node]['config'][$configProperty])) {
            return $config['config_elements'][$node]['config'][$configProperty];
        }

        if (isset($config['inline_config_elements'][$node]['config'][$configProperty])) {
            return $config['inline_config_elements'][$node]['config'][$configProperty];
        }

        return null;
    }

    protected function getAreaConfig(): array
    {
        return $this->areaConfig ?? ($this->areaConfig = $this->getConfigManager()->getAreaConfig($this->getId()));
    }

    protected function getAreaThemeConfig(): array
    {
        return $this->areaThemeConfig ?? ($this->areaThemeConfig = $this->layoutManager->getAreaThemeConfig($this->getId()));
    }

    protected function getAreaThemeOptions(): array
    {
        return $this->areaThemeOptions ?? ($this->areaThemeOptions = $this->getConfigManager()->getConfig('theme'));
    }

    public function setAreaBrickType(string $type = self::AREABRICK_TYPE_INTERNAL): void
    {
        $this->areaBrickType = $type;
    }

    public function getAreaBrickType(): string
    {
        return $this->areaBrickType;
    }

    public function setConfigManager(ConfigManagerInterface $configManager): void
    {
        $this->configManager = $configManager;
    }

    public function getConfigManager(): ConfigManagerInterface
    {
        return $this->configManager;
    }

    public function setLayoutManager(LayoutManagerInterface $layoutManager): void
    {
        $this->layoutManager = $layoutManager;
    }

    public function setInlineConfigBuilder(InlineConfigBuilderInterface $inlineConfigBuilder): void
    {
        $this->inlineConfigBuilder = $inlineConfigBuilder;
    }

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }
}
