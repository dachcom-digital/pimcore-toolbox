<?php

namespace ToolboxBundle\Registry;

class CalculatorRegistry implements CalculatorRegistryInterface
{
    /**
     * @var array
     */
    protected $adapter = [
        'column'       => [],
        'slide_column' => []
    ];

    /**
     * @var string
     */
    private $columnInterface;
    /**
     * @var string
     */
    private $slideColumnInterface;

    /**
     * @param string $columnInterface
     * @param string $slideColumnInterface
     */
    public function __construct($columnInterface, $slideColumnInterface)
    {
        $this->columnInterface = $columnInterface;
        $this->slideColumnInterface = $slideColumnInterface;
    }

    /**
     * {@inheritdoc}
     */
    public function register($id, $service, $type)
    {
        $interface = $type == 'column' ? $this->columnInterface : $this->slideColumnInterface;

        if (!in_array($interface, class_implements($service), true)) {
            throw new \InvalidArgumentException(
                sprintf('%s needs to implement "%s", "%s" given.', get_class($service), $interface, implode(', ', class_implements($service)))
            );
        }

        $this->adapter[$type][$id] = $service;
    }

    /**
     * {@inheritdoc}
     */
    public function has($alias, $type)
    {
        return isset($this->adapter[$type][$alias]);
    }

    /**
     * {@inheritdoc}
     */
    public function getColumnCalculator($alias)
    {
        if (!$this->has($alias, 'column')) {
            throw new \Exception('"' . $alias . '" Column Calculator Identifier does not exist');
        }

        return $this->get($alias, 'column');
    }

    /**
     * {@inheritdoc}
     */
    public function getSlideColumnCalculator($alias)
    {
        if (!$this->has($alias, 'slide_column')) {
            throw new \Exception('"' . $alias . '" Slide Column Calculator Identifier does not exist');
        }

        return $this->get($alias, 'slide_column');
    }

    /**
     * {@inheritdoc}
     */
    public function get($alias, $type)
    {
        if (!$this->has($alias, $type)) {
            throw new \Exception('"' . $alias . '" Calculator Identifier does not exist');
        }

        return $this->adapter[$type][$alias];
    }

}
