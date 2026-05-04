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
    protected string $startDate;
    protected bool $removeAll;
    protected ?int $removedCarts = null;

    public function __construct(string $startDate, bool $removeAll)
    {
        $this->setStartDate($startDate);
        $this->setRemoveAll($removeAll);
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function setStartDate(string $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getRemoveAll(): bool
    {
        return $this->removeAll;
    }

    public function setRemoveAll(bool $removeAll): void
    {
        $this->removeAll = $removeAll;
    }

    public function getRemovedCarts(): ?int
    {
        return $this->removedCarts;
    }

    public function setRemovedCarts(int $removedCarts): void
    {
        $this->removedCarts = $removedCarts;
    }
}
