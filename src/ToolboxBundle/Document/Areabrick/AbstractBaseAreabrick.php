<?php

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\AbstractAreabrick as PimcoreAbstractAreabrick;
use Pimcore\Model\Document;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Manager\LayoutManager;

abstract class AbstractBaseAreabrick extends PimcoreAbstractAreabrick
{
    public const AREABRICK_TYPE_INTERNAL = 'internal';
    public const AREABRICK_TYPE_EXTERNAL = 'external';
    protected ConfigManagerInterface $configManager;
    protected LayoutManager $layoutManager;
    protected string $areaBrickType = 'internal';

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
        $space = $this->getAreaBrickType() === self::AREABRICK_TYPE_INTERNAL
            ? ConfigManagerInterface::AREABRICK_NAMESPACE_INTERNAL
            : ConfigManagerInterface::AREABRICK_NAMESPACE_EXTERNAL;

        return $this->configManager->setAreaNameSpace($space);
    }

    public function setLayoutManager(LayoutManager $layoutManager): void
    {
        $this->layoutManager = $layoutManager;
    }

    public function action(Document\Editable\Area\Info $info): ?Response
    {
        $configNode = $this->getConfigManager()->getAreaConfig($this->getId());

        $info->setParams(array_merge($info->getParams(), [
            'additionalClassesData' => $this->configureAdditionalClasses($info, $configNode),
            'elementThemeConfig'    => $this->layoutManager->getAreaThemeConfig($this->getId()),
            'areaId'                => $this->getId(),
            'areaTemplateDirectory' => $this->getTemplateDirectoryName(),
        ]));

        return null;
    }

    private function configureAdditionalClasses(Document\Editable\Area\Info $info, array $configNode): array
    {
        $classesArray = [];

        if (!isset($configNode['config_elements'])) {
            return $classesArray;
        }

        foreach ($configNode['config_elements'] as $name => $configElement) {
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
}
