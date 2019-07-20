<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ImageThumbnailExtension extends AbstractExtension
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * @param ConfigManagerInterface $configManager
     */
    public function __construct(ConfigManagerInterface $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('toolbox_get_image_thumbnail', [$this, 'getImageThumbnail'])
        ];
    }

    /**
     * @param null $thumbnailName
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function getImageThumbnail($thumbnailName = null)
    {
        return $this->configManager->getImageThumbnailFromConfig($thumbnailName);
    }
}
