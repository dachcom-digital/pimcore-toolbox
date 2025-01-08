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

namespace ToolboxBundle\Registry;

use ToolboxBundle\Calculator\ColumnCalculatorInterface;
use ToolboxBundle\Calculator\SlideColumnCalculatorInterface;

class CalculatorRegistry implements CalculatorRegistryInterface
{
    protected array $adapter = [
        'column'       => [],
        'slide_column' => []
    ];

    public function __construct(protected string $columnInterface, protected string $slideColumnInterface)
    {
    }

    public function register(string $id, mixed $service, string $type): void
    {
        $interface = $type === 'column' ? $this->columnInterface : $this->slideColumnInterface;

        if (!in_array($interface, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), $interface, implode(', ', class_implements($service)))
            );
        }

        $this->adapter[$type][$id] = $service;
    }

    public function has(string $alias, string $type): bool
    {
        return isset($this->adapter[$type][$alias]);
    }

    public function getColumnCalculator($alias): ColumnCalculatorInterface
    {
        if (!$this->has($alias, 'column')) {
            throw new \Exception('"' . $alias . '" Column Calculator Identifier does not exist');
        }

        return $this->get($alias, 'column');
    }

    public function getSlideColumnCalculator($alias): SlideColumnCalculatorInterface
    {
        if (!$this->has($alias, 'slide_column')) {
            throw new \Exception('"' . $alias . '" Slide Column Calculator Identifier does not exist');
        }

        return $this->get($alias, 'slide_column');
    }

    public function get($alias, $type): SlideColumnCalculatorInterface|ColumnCalculatorInterface
    {
        if (!$this->has($alias, $type)) {
            throw new \Exception('"' . $alias . '" Calculator Identifier does not exist');
        }

        return $this->adapter[$type][$alias];
    }
}
