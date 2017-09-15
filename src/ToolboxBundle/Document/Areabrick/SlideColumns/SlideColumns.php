<?php

namespace ToolboxBundle\Document\Areabrick\SlideColumns;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class SlideColumns extends AbstractAreabrick
{
    /**
     * @var  SlideColumnCalculatorInterface
     */
    protected $calculator;

    /**
     * @param SlideColumnCalculatorInterface $calculator
     */
    public function setCalculator(SlideColumnCalculatorInterface $calculator)
    {
        $this->calculator = $calculator;
    }

    /**
     * @param Info $info
     */
    public function action(Info $info)
    {
        parent::action($info);

        $view = $info->getView();

        $equalHeight = $this->getDocumentTag($info->getDocument(), 'checkbox', 'equal_height')->isChecked() && !$info->getView()->get('editmode');
        $id = $info->getView()->get('brick')->getId() . '-' . $info->getView()->get('brick')->getIndex();

        $slidesPerView = (int) $this->getDocumentTag($info->getDocument(), 'select', 'slides_per_view')->getData();
        $slideElements = $this->getDocumentTag($info->getDocument(), 'block', 'slideCols', ['default' => $slidesPerView]);

        $slideColumnConfig = $this->getConfigManager()->getAreaParameterConfig('slideColumns');
        $slidesPerViewClass = $this->calculator->calculateSlideColumnClasses($slidesPerView, $slideColumnConfig);
        $breakpoints = $this->calculateSlideColumnBreakpoints($slidesPerView);

        $view->id = $id;
        $view->slideElements = $slideElements;
        $view->slidesPerView = $slidesPerView;
        $view->slidesPerViewClasses = $slidesPerViewClass;
        $view->breakpoints = $breakpoints;
        $view->equalHeight = $equalHeight;
    }

    public function getViewTemplate()
    {
        return 'ToolboxBundle:Areas/slideColumns:view.' . $this->getTemplateSuffix();
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
     *
     * @return array
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