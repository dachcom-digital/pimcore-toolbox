<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManager;

class ImageThumbnailExtension extends \Twig_Extension
{
    /**
     * @var ConfigManager
     */
    protected $configManager;

    /**
     * ImageThumbnailExtension constructor.
     *
     * @param ConfigManager $configManager
     */
    public function __construct(ConfigManager $configManager)
    {
        $this->configManager = $configManager;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new \Twig_Function('toolbox_get_image_thumbnail', [$this, 'getImageThumbnail'])
        ];
    }

    /**
     * @param null $thumbnailName
     *
     * @return mixed
     */
    public function getImageThumbnail($thumbnailName = NULL)
    {
        return $this->configManager->getImageThumbnailFromConfig($thumbnailName);
    }
}