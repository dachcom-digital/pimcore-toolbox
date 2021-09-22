<?php

namespace ToolboxBundle\Document\Areabrick\SlideColumns;

use Pimcore\Model\Document\Editable\Checkbox;
use Pimcore\Model\Document\Editable\Area\Info;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Registry\CalculatorRegistryInterface;

class SlideColumns extends AbstractAreabrick
{
    /**
     * @var CalculatorRegistryInterface
     */
    private $calculatorRegistry;

    /**
     * @param CalculatorRegistryInterface $calculatorRegistry
     */
    public function __construct(CalculatorRegistryInterface $calculatorRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    public function action(Info $info): ?Response
    {
        parent::action($info);

        /** @var Checkbox $equalHeightElement */
        $equalHeightElement = $this->getDocumentEditable($info->getDocument(), 'checkbox', 'equal_height');
        $equalHeight = $equalHeightElement->isChecked() && !$info->getParam('editmode');

        $id = sprintf('%s-%s', $info->getId(), $info->getIndex());

        $slidesPerView = (int) $this->getDocumentEditable($info->getDocument(), 'select', 'slides_per_view')->getData();
        $slideElements = $this->getDocumentEditable($info->getDocument(), 'block', 'slideCols', ['default' => $slidesPerView]);

        $theme = $this->configManager->getConfig('theme');
        $calculator = $this->calculatorRegistry->getSlideColumnCalculator($theme['calculators']['slide_calculator']);

        $slideColumnConfig = $this->getConfigManager()->getAreaParameterConfig('slideColumns');
        $slidesPerViewClass = $calculator->calculateSlideColumnClasses($slidesPerView, $slideColumnConfig);
        $breakpoints = $this->calculateSlideColumnBreakpoints($slidesPerView);

        $info->setParams(array_merge($info->getParams(), [
            'id'                   => $id,
            'slideElements'        => $slideElements,
            'slidesPerView'        => $slidesPerView,
            'slidesPerViewClasses' => $slidesPerViewClass,
            'breakpoints'          => $breakpoints,
            'equalHeight'          => $equalHeight
        ]));

        return null;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'Slide Columns';
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return 'Toolbox Slide Columns';
    }

    /**
     * @param int $columnType
     *
     * @return array
     *
     * @throws \Exception
     */
    private function calculateSlideColumnBreakpoints($columnType)
    {
        $columnType = (int) $columnType;
        $configInfo = $this->getConfigManager()->getAreaParameterConfig('slideColumns');

        $breakpoints = [];

        if (!empty($configInfo)) {
            if (isset($configInfo['breakpoints']) && isset($configInfo['breakpoints'][$columnType])) {
                $breakpoints = $configInfo['breakpoints'][$columnType];
            }
        }

        return $breakpoints;
    }
}
