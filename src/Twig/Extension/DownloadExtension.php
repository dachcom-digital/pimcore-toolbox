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

namespace ToolboxBundle\Twig\Extension;

use ToolboxBundle\Manager\ConfigManagerInterface;
use ToolboxBundle\Service\DownloadInfoService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class DownloadExtension extends AbstractExtension
{
    public function __construct(
        protected ConfigManagerInterface $configManager,
        protected DownloadInfoService $downloadInfoService
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_download_info', [$this->downloadInfoService, 'getDownloadInfo']),
            new TwigFunction('toolbox_download_tracker', [$this, 'getDownloadTracker'], ['is_safe' => ['html']])
        ];
    }

    /**
     * @throws \Exception
     */
    public function getDownloadTracker(mixed $areaType, mixed $element = null): string
    {
        if (empty($areaType)) {
            return '';
        }

        if (is_array($areaType)) {
            $trackerInfo = $areaType;
        } else {
            $configNode = $this->configManager->getAreaParameterConfig($areaType);

            if (empty($configNode) || !isset($configNode['event_tracker'])) {
                return '';
            }

            $trackerInfo = $configNode['event_tracker'];
        }

        $str = 'data-tracking="active" ';

        $str .= implode(' ', array_map(static function ($key) use ($trackerInfo, $element) {
            $val = $trackerInfo[$key];

            if (is_bool($val)) {
                $val = (int) $val;
            }

            if ($key === 'label' && is_array($val)) {
                $getter = $val;
                $val = call_user_func_array([$element, $getter[0]], $getter[1]);

                if (empty($val)) {
                    $val = 'no label given';
                }
            }

            return 'data-' . $key . '="' . $val . '"';
        }, array_keys($trackerInfo)));

        return $str;
    }
}
