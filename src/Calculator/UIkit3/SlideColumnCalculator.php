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

namespace ToolboxBundle\Calculator\UIkit3;

use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class SlideColumnCalculator implements SlideColumnCalculatorInterface
{
    /**
     * @param int   $columnType
     * @param array $columnConfiguration
     *
     * @return string
     */
    public function calculateSlideColumnClasses(int $columnType, array $columnConfiguration): string
    {
        $systemClasses = [
            2 => 'uk-child-width-1-2@s uk-child-width-1-1',
            3 => 'uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1',
            4 => 'uk-child-width-1-4@l uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1',
            6 => 'uk-child-width-1-6@l uk-child-width-1-3@m uk-child-width-1-2@s uk-child-width-1-1 ',
        ];

        if (empty($columnConfiguration)) {
            return $systemClasses[$columnType] ?? 'uk-child-width-1-1';
        }

        if (!isset($columnConfiguration['column_classes'][$columnType])) {
            return $systemClasses[$columnType] ?? 'uk-child-width-1-1';
        }

        return $columnConfiguration['column_classes'][$columnType];
    }
}
