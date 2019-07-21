<?php

namespace DachcomBundle\Test\UnitDefault\Areas;

use Pimcore\Model\Document\Tag\Checkbox;
use Pimcore\Model\Document\Tag\Relations;
use Pimcore\Model\Document\Tag\Select;
use Pimcore\Tests\Util\TestHelper;

class DownloadTest extends AbstractAreaTest
{
    const TYPE = 'download';

    public function testDownloadBackendConfig()
    {
        $this->setupRequest();

        $areaConfig = $this->generateBackendArea(self::TYPE);
        $configElements = $areaConfig['config_elements'];

        $this->assertCount(3, $configElements);
        $this->assertEquals('relations', $configElements[0]['additional_config']['type']);
        $this->assertEquals('downloads', $configElements[0]['additional_config']['name']);

        $this->assertEquals('checkbox', $configElements[1]['additional_config']['type']);
        $this->assertEquals('show_preview_images', $configElements[1]['additional_config']['name']);

        $this->assertEquals('checkbox', $configElements[2]['additional_config']['type']);
        $this->assertEquals('show_file_info', $configElements[2]['additional_config']['name']);
    }

    public function testDownload()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $downloads = new Relations();
        $downloads->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $elements = [
            'downloads' => $downloads
        ];

        $this->assertEquals(
            $this->filter($this->getCompare($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testDownloadWithPreviewImage()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $downloads = new Relations();
        $downloads->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'downloads'           => $downloads,
            'show_preview_images' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithPreviewImage($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testDownloadWithFileInfo()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $downloads = new Relations();
        $downloads->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $checkbox = new Checkbox();
        $checkbox->setDataFromResource(1);

        $elements = [
            'downloads'      => $downloads,
            'show_file_info' => $checkbox
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithFileInfo($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    public function testDownloadWithAdditionalClass()
    {
        $this->setupRequest();

        $asset1 = TestHelper::createImageAsset('', true);
        $asset2 = TestHelper::createImageAsset('', true);

        $downloads = new Relations();
        $downloads->setDataFromEditmode([
            [
                'id'   => $asset1->getId(),
                'type' => 'asset'
            ],
            [
                'id'   => $asset2->getId(),
                'type' => 'asset'
            ]
        ]);

        $combo = new Select();
        $combo->setDataFromResource('additional-class');

        $elements = [
            'downloads'   => $downloads,
            'add_classes' => $combo
        ];

        $this->assertEquals(
            $this->filter($this->getCompareWithAdditionalClass($asset1->getFullPath(), $asset2->getFullPath())),
            $this->filter($this->generateRenderedArea(self::TYPE, $elements))
        );
    }

    private function getCompare($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-download ">
                    <div class="download-list">
                        <ul class="list-unstyled">
                            <li>
                                <a href="' . $path1 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="title">Download</span>
                                </a>
                            </li>
                            <li>
                                <a href="' . $path2 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="title">Download</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';
    }

    private function getCompareWithPreviewImage($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-download ">
                    <div class="download-list show-image-preview">
                        <ul class="list-unstyled">
                            <li>
                                <a href="' . $path1 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="preview-image"><img src="' . $path1 . '" alt="Download"/></span>
                                    <span class="title">Download</span>
                                </a>
                            </li>
                            <li>
                                <a href="' . $path2 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="preview-image"><img src="' . $path2 . '" alt="Download"/></span>
                                    <span class="title">Download</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';
    }

    private function getCompareWithFileInfo($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-download ">
                    <div class="download-list">
                        <ul class="list-unstyled">
                            <li>
                                <a href="' . $path1 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="title">Download</span>
                                    <span class="file-info">(<span class="file-type">jpg</span>, 1 byte)</span>
                                </a>
                            </li>
                            <li>
                                <a href="' . $path2 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="title">Download</span>
                                    <span class="file-info">(<span class="file-type">jpg</span>, 1 byte)</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';
    }

    private function getCompareWithAdditionalClass($path1, $path2)
    {
        return '<div class="toolbox-element toolbox-download additional-class">
                    <div class="download-list">
                        <ul class="list-unstyled">
                            <li>
                                <a href="' . $path1 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="title">Download</span>
                                </a>
                            </li>
                            <li>
                                <a href="' . $path2 . '"  target="_blank" class="icon-download-jpg">
                                    <span class="title">Download</span>
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>';
    }
}
