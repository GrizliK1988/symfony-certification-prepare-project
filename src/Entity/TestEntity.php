<?php
/**
 * Created by PhpStorm.
 * User: dima
 * Date: 26.10.15
 * Time: 20:56
 */

namespace DG\SymfonyCert\Entity;


class TestEntity
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $message;

    /**
     * @var bool
     */
    private $active;

    /**
     * @var string
     */
    private $testVar;

    /**
     * @return boolean
     */
    public function isActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage($message)
    {
        $this->message = $message;
    }

    /**
     * @return string
     */
    public function getTestVar()
    {
        return $this->testVar;
    }

    /**
     * @param string $testVar
     */
    public function setTestVar($testVar)
    {
        $this->testVar = $testVar;
    }
} 