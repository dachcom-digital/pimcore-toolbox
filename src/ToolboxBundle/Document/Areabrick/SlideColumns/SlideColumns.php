<?php

namespace ToolboxBundle\Document\Areabrick\SlideColumns;

use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use Pimcore\Model\Document\Tag\Area\Info;

class SlideColumns extends AbstractAreabrick
{
    public function action(Info $info)
    {
        $view = $info->getView();
        $view->elementConfigBar = $this->getElementBuilder()->buildElementConfig($this->getId(), $this->getName(), $info);

        $equalHeight = $this->getDocumentTag($info->getDocument(), 'checkbox', 'equalHeight')->isChecked() && !$info->getView()->get('editmode');
        $id = $info->getView()->get('brick')->getId() . '-' . $info->getView()->get('brick')->getIndex();

        $slidesPerView = (int) $this->getDocumentTag($info->getDocument(), 'select', 'slidesPerView')->getData();
        $slideElements = $this->getDocumentTag($info->getDocument(), 'block', 'slideCols', ['default' => $slidesPerView]);

        $slidesPerViewClass = $this->calculateSlideColumnClasses($slidesPerView);
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
        return 'ToolboxBundle:Areas/SlideColumns:view.' . $this->getTemplateSuffix();
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
     * @return string
     */
    private function calculateSlideColumnClasses($columnType)
    {
        $columnType = (int)$columnType;
        $configInfo = $this->getConfigManager()->getAreaParameterConfig('slideColumns');

        $systemClasses = [
            2 => 'col-xs-12 col-sm-6',
            3 => 'col-xs-12 col-sm-4',
            4 => 'col-xs-12 col-sm-3',
            6 => 'col-xs-12 col-sm-2',

        ];

        if (empty($configInfo)) {
            return isset($systemClasses[$columnType]) ? $systemClasses[$columnType] : 'col-xs-12';
        }

        if (!isset($configInfo['columnClasses']) || !isset($configInfo['columnClasses'][$columnType])) {
            return isset($systemClasses[$columnType]) ? $systemClasses[$columnType] : 'col-xs-12';
        }

        return $configInfo['columnClasses'][$columnType];
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