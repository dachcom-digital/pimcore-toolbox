<?php

namespace ToolboxBundle\Document\Areabrick\SlideColumns;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;
use ToolboxBundle\Registry\CalculatorRegistry;

class SlideColumns extends AbstractAreabrick
{
    /**
     * @var CalculatorRegistry
     */
    private $calculatorRegistry;

    /**
     * @param CalculatorRegistry $calculatorRegistry
     */
    public function __construct(CalculatorRegistry $calculatorRegistry)
    {
        $this->calculatorRegistry = $calculatorRegistry;
    }

    /**
     * @param Info $info
     * @return null|\Symfony\Component\HttpFoundation\Response|void
     * @throws \Exception
     */
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        $equalHeight = $this->getDocumentTag($info->getDocument(), 'checkbox', 'equal_height')->isChecked() && !$info->getView()->get('editmode');
        $id = $info->getView()->get('brick')->getId() . '-' . $info->getView()->get('brick')->getIndex();

        $slidesPerView = (int) $this->getDocumentTag($info->getDocument(), 'select', 'slides_per_view')->getData();
        $slideElements = $this->getDocumentTag($info->getDocument(), 'block', 'slideCols', ['default' => $slidesPerView]);

        $theme = $this->configManager->getConfig('theme');
        $calculator = $this->calculatorRegistry->get($theme['calculators']['slide_calculator'], 'slide_column');

        $slideColumnConfig = $this->getConfigManager()->getAreaParameterConfig('slideColumns');
        $slidesPerViewClass = $calculator->calculateSlideColumnClasses($slidesPerView, $slideColumnConfig);
        $breakpoints = $this->calculateSlideColumnBreakpoints($slidesPerView);

        $view->id = $id;
        $view->slideElements = $slideElements;
        $view->slidesPerView = $slidesPerView;
        $view->slidesPerViewClasses = $slidesPerViewClass;
        $view->breakpoints = $breakpoints;
        $view->equalHeight = $equalHeight;
    }

    public function getName()
    {
        return 'Slide Columns';
    }

    public function getDescription()
    {
        return 'Toolbox Slide Columns';
    }

    /**
     * @param $columnType
     * @return array
     * @throws \Exception
     */
    private function calculateSlideColumnBreakpoints($columnType)
    {
        $columnType = (int)$columnType;
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