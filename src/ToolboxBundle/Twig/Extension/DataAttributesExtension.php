<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Service\DataAttributeService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DataAttributesExtension extends AbstractExtension
{
    public function __construct(protected DataAttributeService $dataAttributeService)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_data_attributes_generator', [$this->dataAttributeService, 'generateDataAttributes']),
        ];
    }

}
