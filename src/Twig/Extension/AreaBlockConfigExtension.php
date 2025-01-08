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

use Pimcore\Model\Document\Snippet;
use ToolboxBundle\Manager\AreaManagerInterface;
use ToolboxBundle\Manager\ConfigManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AreaBlockConfigExtension extends AbstractExtension
{
    public function __construct(protected ConfigManagerInterface $configManager, protected AreaManagerInterface $areaManager)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('toolbox_areablock_config', [$this, 'getAreaBlockConfiguration'], [
                'needs_context' => true
            ])
        ];
    }

    /**
     * @throws \Exception
     */
    public function getAreaBlockConfiguration(array $context = [], $type = null): array
    {
        $document = $context['document'];
        $editMode = $context['editmode'] ?? false;

        return $this->areaManager->getAreaBlockConfiguration($type, $document instanceof Snippet, $editMode);
    }
}
