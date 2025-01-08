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

namespace ToolboxBundle\Calculator\Bootstrap3;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    public function calculateSlideColumnClasses(int $columnType, array $columnConfiguration): string
    {
        $systemClasses = [
            2 => 'col-xs-12 col-sm-6',
            3 => 'col-xs-12 col-sm-4',
            4 => 'col-xs-12 col-sm-3',
            6 => 'col-xs-12 col-sm-2',
        ];

        if (empty($columnConfiguration)) {
            return $systemClasses[$columnType] ?? 'col-xs-12';
        }

        if (!isset($columnConfiguration['column_classes']) || !isset($columnConfiguration['column_classes'][$columnType])) {
            return $systemClasses[$columnType] ?? 'col-xs-12';
        }

        return $columnConfiguration['column_classes'][$columnType];
    }
}
