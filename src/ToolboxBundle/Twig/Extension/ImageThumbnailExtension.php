<?php

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;

class ImageThumbnailExtension extends \Twig_Extension
{
    /**
     * @var ConfigManagerInterface
     */
    protected $configManager;

    /**
     * ImageThumbnailExtension constructor.
     *
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
            new \Twig_Function('toolbox_get_image_thumbnail', [$this, 'getImageThumbnail'])
        ];
    }

    /**
     * @param null $thumbnailName
     * @return mixed
     * @throws \Exception
     */
    public function getImageThumbnail($thumbnailName = null)
    {
        return $this->configManager->getImageThumbnailFromConfig($thumbnailName);
    }
}