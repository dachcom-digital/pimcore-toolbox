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

namespace ToolboxBundle\Document\Areabrick;

use Pimcore\Extension\Document\Areabrick\EditableDialogBoxConfiguration;
use Pimcore\Extension\Document\Areabrick\EditableDialogBoxInterface;
use Pimcore\Model\Document;
use ToolboxBundle\Builder\BrickConfigBuilderInterface;
use ToolboxBundle\Document\Response\HeadlessResponse;

abstract class AbstractAreabrick extends AbstractBaseAreabrick implements EditableDialogBoxInterface
{
    protected BrickConfigBuilderInterface $brickConfigBuilder;

    public function setBrickConfigBuilder(BrickConfigBuilderInterface $brickConfigBuilder): void
    {
        $this->brickConfigBuilder = $brickConfigBuilder;
    }

    public function headlessAction(Document\Editable\Area\Info $info, HeadlessResponse $headlessResponse): void
    {
        parent::headlessAction($info, $headlessResponse);

        $headlessResponse->setConfigElementData(
            $this->brickConfigBuilder->buildConfigurationData(
                $info,
                $this->getId(),
                $this->getAreaConfig(),
                $this->getAreaThemeOptions(),
                $this->isHeadlessLayoutAware()
            )
        );

        $this->triggerHeadlessEditableActionEvent($info, $headlessResponse);
    }

    public function getEditableDialogBoxConfiguration(Document\Editable $area, ?Document\Editable\Area\Info $info): EditableDialogBoxConfiguration
    {
        return $this->brickConfigBuilder->buildConfiguration(
            $info,
            $this->getId(),
            $this->getAreaConfig(),
            $this->getAreaThemeOptions(),
            $this->isHeadlessLayoutAware()
        );
    }
}
