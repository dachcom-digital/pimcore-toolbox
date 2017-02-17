<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class SlideColumns extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $adminData = NULL;
        if ($this->getView()->editmode) {
            $adminData = \Toolbox\Tool\ElementBuilder::buildElementConfig('slideColumns', $this->getView());
        }

        $equalHeight = $this->getView()->checkbox('equalHeight')->isChecked() && !$this->getView()->editmode;
        $id = $this->getView()->brick->getId() . '-' . $this->getView()->brick->getIndex();
        $slidesPerView = (int) $this->getView()->select('slidesPerView')->getData();
        $slideElements = $this->getView()->block('slideCols', ['default' => $slidesPerView]);
        $slidesPerViewClass = $this->getView()->toolboxHelper()->calculateSlideColumnClasses($slidesPerView);
        $breakpoints = $this->getView()->toolboxHelper()->calculateSlideColumnBreakpoints($slidesPerView);

        $this->getView()->assign(
            [
                'adminData'            => $adminData,
                'id'                   => $id,
                'slideElements'        => $slideElements,
                'slidesPerView'        => $slidesPerView,
                'slidesPerViewClasses' => $slidesPerViewClass,
                'breakpoints'          => $breakpoints,
                'equalHeight'          => $equalHeight
            ]
        );
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