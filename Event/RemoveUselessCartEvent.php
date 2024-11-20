<?php

namespace RemoveUselessCart\Event;

use Thelia\Core\Event\ActionEvent;

/**
 * Class RemoveUselessCartEvent
 * @package RemoveUselessCart\Event
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class RemoveUselessCartEvent extends ActionEvent
{
    protected mixed $startDate;
    protected mixed $removeAll;
    protected mixed $removedCarts;

    public function __construct($startDate, $removeAll)
    {
        $this->setStartDate($startDate);
        $this->setRemoveAll($removeAll);
    }

    /**
     * @return mixed
     */
    public function getStartDate(): mixed
    {
        return $this->startDate;
    }

    /**
     * @param mixed $startDate
     */
    public function setStartDate($startDate): void
    {
        $this->startDate = $startDate;
    }

    /**
     * @return mixed
     */
    public function getRemoveAll(): mixed
    {
        return $this->removeAll;
    }

    /**
     * @param mixed $removeAll
     */
    public function setRemoveAll($removeAll): void
    {
        $this->removeAll = $removeAll;
    }

    /**
     * @return mixed
     */
    public function getRemovedCarts(): mixed
    {
        return $this->removedCarts;
    }

    /**
     * @param mixed $removedCarts
     */
    public function setRemovedCarts($removedCarts): void
    {
        $this->removedCarts = $removedCarts;
    }
}
