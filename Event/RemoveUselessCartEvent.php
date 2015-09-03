<?php

namespace RemoveUselessCart\Event;

use Thelia\Core\Event\ActionEvent;

class RemoveUselessCartEvent extends ActionEvent
{
    protected $startDate;
    protected $removeAll;
    protected $removedCarts;

    public function __construct($startDate, $removeAll)
    {
        $this->startDate = $startDate;
        $this->removeAll = $removeAll;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getRemoveAll()
    {
        return $this->removeAll;
    }

    /**
     * @param mixed $removeAll
     */
    public function setRemoveAll($removeAll)
    {
        $this->removeAll = $removeAll;
    }

    /**
     * @return mixed
     */
    public function getRemovedCarts()
    {
        return $this->removedCarts;
    }

    /**
     * @param mixed $removedCarts
     */
    public function setRemovedCarts($removedCarts)
    {
        $this->removedCarts = $removedCarts;
    }
}
