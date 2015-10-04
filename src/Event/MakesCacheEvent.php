<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 30.09.15
 * Time: 20:16
 */

namespace DG\SymfonyCert\Event;


use Symfony\Component\EventDispatcher\Event;

class MakesCacheEvent extends Event
{
    private $makes;
    private $state;
    private $year;
    private $view;

    function __construct(array $makes, $state, $year, $view)
    {
        $this->makes = $makes;
        $this->state = $state;
        $this->year = $year;
        $this->view = $view;
    }

    /**
     * @return array
     */
    public function getMakes()
    {
        return $this->makes;
    }

    /**
     * @param array $makes
     */
    public function setMakes($makes)
    {
        $this->makes = $makes;
    }

    /**
     * @return mixed
     */
    public function getState()
    {
        return $this->state;
    }

    /**
     * @param mixed $state
     */
    public function setState($state)
    {
        $this->state = $state;
    }

    /**
     * @return mixed
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param mixed $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }

    /**
     * @return mixed
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @param mixed $year
     */
    public function setYear($year)
    {
        $this->year = $year;
    }

}