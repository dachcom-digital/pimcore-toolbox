<?php

namespace Pimcore\Model\Document\Tag\Area;

use Pimcore\Model\Document;

class Teaser extends Document\Tag\Area\AbstractArea
{
    public function action()
    {
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