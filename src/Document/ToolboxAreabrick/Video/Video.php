<?php

/*
 * This source file is available under two different licenses:
 *   - GNU General Public License version 3 (GPLv3)
 *   - DACHCOM Commercial License (DCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) DACHCOM.DIGITAL AG (https://www.dachcom-digital.com)
 * @license    GPLv3 and DCL
 */

namespace ToolboxBundle\Document\ToolboxAreabrick\Video;

use Pimcore\Model\Asset;
use Pimcore\Model\Document\Editable\Area\Info;
use Pimcore\Model\Document\Editable\Checkbox;
use Symfony\Component\HttpFoundation\Response;
use ToolboxBundle\Document\Areabrick\AbstractAreabrick;
use ToolboxBundle\Document\Areabrick\ToolboxHeadlessAwareBrickInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;
use ToolboxBundle\Model\Document\Editable\Vhs;

class Video extends AbstractAreabrick implements ToolboxHeadlessAwareBrickInterface
{
    public function action(Info $info): ?Response
    {
        $this->buildInfoParameters($info);

        return parent::action($info);
    }

    public function headlessAction(Info $info, HeadlessResponse $headlessResponse): void
    {
        $this->buildInfoParameters($info, $headlessResponse);

        parent::headlessAction($info, $headlessResponse);
    }

    protected function buildInfoParameters(Info $info, ?HeadlessResponse $headlessResponse = null): void
    {
        /** @var Vhs $videoTag */
        $videoTag = $this->getDocumentEditable($info->getDocument(), 'vhs', 'video');

        $videoParameter = $videoTag->getVideoParameter();

        $playInLightBox = $videoTag->getShowAsLightBox() === true ? 'true' : 'false';
        /** @var Checkbox $autoPlayElement */
        $autoPlayElement = $this->getDocumentEditable($info->getDocument(), 'checkbox', 'autoplay');
        $autoPlay = $autoPlayElement->isChecked() === true && !$info->getEditable()->getEditmode();
        $videoType = $videoTag->getVideoType();
        $posterPath = null;
        $poster = $videoTag->getPoster() ? $videoTag->getPosterAsset() : null;

        if ($poster instanceof Asset\Image) {
            $imageThumbnail = $this->getConfigManager()->getImageThumbnailFromConfig('video_poster');
            $posterPath = $poster->getThumbnail($imageThumbnail);
        }

        $brickParams = [
            'autoPlay'       => $autoPlay,
            'posterPath'     => $posterPath,
            'videoType'      => $videoType,
            'playInLightbox' => $playInLightBox,
            'videoParameter' => $videoParameter,
            'videoId'        => $videoTag->getId()
        ];

        if ($headlessResponse instanceof HeadlessResponse) {
            $headlessResponse->setAdditionalConfigData($brickParams);

            return;
        }

        $info->setParams(array_merge($info->getParams(), $brickParams));
    }

    public function getName(): string
    {
        return 'Video';
    }

    public function getDescription(): string
    {
        return 'Toolbox Video';
    }
}
