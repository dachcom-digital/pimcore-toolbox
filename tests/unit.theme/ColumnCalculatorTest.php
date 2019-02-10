<?php

namespace DachcomBundle\Test\UnitTheme;

use DachcomBundle\Test\Test\DachcomBundleTestCase;
use ToolboxBundle\Calculator\ColumnCalculatorInterface;

class ColumnCalculatorTest extends DachcomBundleTestCase
{
    public function testDefaultColumnCalculatorB3()
    {
        /** @var ColumnCalculatorInterface $columnCalculator */
        $columnCalculator = $this->getContainer()->get(\ToolboxBundle\Calculator\Bootstrap3\ColumnCalculator::class);
        $calculatedValue = $columnCalculator->calculateColumns('column_4_4_4');

        $expected = [
            [
                'columnClass' => 'col-xs-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_0'
            ],
            [
                'columnClass' => 'col-xs-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_1'
            ],
            [
                'columnClass' => 'col-xs-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_2'
            ]
        ];

        $this->assertEquals($expected, $calculatedValue);
    }

    public function testDefaultColumnCalculatorB4()
    {
        /** @var ColumnCalculatorInterface $columnCalculator */
        $columnCalculator = $this->getContainer()->get(\ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator::class);
        $calculatedValue = $columnCalculator->calculateColumns('column_4_4_4');

        $expected = [
            [
                'columnClass' => 'col-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_0'
            ],
            [
                'columnClass' => 'col-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_1'
            ],
            [
                'columnClass' => 'col-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_2'
            ]
        ];

        $this->assertEquals($expected, $calculatedValue);
    }

    public function testOffsetColumnCalculatorB4()
    {
        /** @var ColumnCalculatorInterface $columnCalculator */
        $columnCalculator = $this->getContainer()->get(\ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator::class);
        $calculatedValue = $columnCalculator->calculateColumns('column_o1_4_4_4');

        $expected = [
            [
                'columnClass' => 'col-12 col-sm-4 offset-sm-1',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => [
                        'sm' => 1
                    ]
                ],
                'columnType'  => 'column_o1_4_4_4',
                'name'        => 'column_0'
            ],
            [
                'columnClass' => 'col-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_o1_4_4_4',
                'name'        => 'column_1'
            ],
            [
                'columnClass' => 'col-12 col-sm-4 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 12,
                        'sm' => 4
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_o1_4_4_4',
                'name'        => 'column_2'
            ]
        ];

        $this->assertEquals($expected, $calculatedValue);
    }

    public function testAdjustedColumnCalculatorB4()
    {
        /** @var ColumnCalculatorInterface $columnCalculator */
        $columnCalculator = $this->getContainer()->get(\ToolboxBundle\Calculator\Bootstrap4\ColumnCalculator::class);
        $custom = [
            'column_4_4_4' => [
                'breakpoints' => [
                    'xs' => '3_6_3',
                    'md' => '4_4_4',
                    'lg' => '2_2_8'
                ]
            ]
        ];

        $calculatedValue = $columnCalculator->calculateColumns('column_4_4_4', $custom);

        $expected = [
            [
                'columnClass' => 'col-3 col-md-4 col-lg-2 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 3,
                        'md' => 4,
                        'lg' => 2
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_0'
            ],
            [
                'columnClass' => 'col-6 col-md-4 col-lg-2 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 6,
                        'md' => 4,
                        'lg' => 2
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_1'
            ],
            [
                'columnClass' => 'col-3 col-md-4 col-lg-8 ',
                'columnData'  => [
                    'grid'       => [
                        'xs' => 3,
                        'md' => 4,
                        'lg' => 8
                    ],
                    'gridOffset' => []
                ],
                'columnType'  => 'column_4_4_4',
                'name'        => 'column_2'
            ]
        ];

        $this->assertEquals($expected, $calculatedValue);
    }
}
