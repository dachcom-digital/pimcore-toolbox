<?php

namespace Toolbox\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormFactoryInterface;

abstract class Base extends TestCase
{
    /**
     * Print Test Name.
     */
    public function printTestName()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            echo '### running ...  '.$trace[1]['class'].'::'.$trace[1]['function']." ... good luck!\n";
        }
    }

    /**
     * Print TO-DO Test Name.
     */
    public function printTodoTestName()
    {
        try {
            throw new \Exception();
        } catch (\Exception $e) {
            $trace = $e->getTrace();
            echo '### running ...  '.$trace[1]['class'].'::'.$trace[1]['function']." ... good luck! TODO! \n";
        }
    }

    /**
     * Setup Test.
     */
    public function setUp()
    {
    }

    /**
     * @return FormFactoryInterface
     */
    protected function getFormFactory()
    {
        return $this->get('form.factory');
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    protected function get($id)
    {
        return \Pimcore::getKernel()->getContainer()->get($id);
    }
}
