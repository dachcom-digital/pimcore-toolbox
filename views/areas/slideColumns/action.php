<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class SlideColumns extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
        $equalHeight = $this->view->checkbox('equalHeight')->isChecked() && !$this->view->editmode;
        $id = $this->view->brick->getId() . '-' . $this->view->brick->getIndex();
        $slidesPerView = (int)$this->view->select('slidesPerView')->getData();
        $slideElements = $this->view->block('slideCols', ['default' => $slidesPerView]);
        $slidesPerViewClass = $this->view->toolboxHelper()->calculateSlideColumnClasses($slidesPerView);
        $breakpoints = $this->view->toolboxHelper()->calculateSlideColumnBreakpoints($slidesPerView);

        $this->view->assign(
            [
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