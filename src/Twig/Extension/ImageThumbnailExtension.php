<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageThumbnailExtension extends AbstractExtension
{
    public function __construct(protected ConfigManagerInterface $configManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_get_image_thumbnail', [$this, 'getImageThumbnail'])
        ];
    }

    /**
     * @throws \Exception
     */
    public function getImageThumbnail(string $thumbnailName): ?string
    {
        return $this->configManager->getImageThumbnailFromConfig($thumbnailName);
    }
}
