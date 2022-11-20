<?php

namespace RemoveUselessCart\EventListeners;

use Propel\Runtime\Propel;
use RemoveUselessCart\Event\RemoveUselessCartEvent;
use RemoveUselessCart\Event\RemoveUselessCartEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Install\Database;
use Thelia\Model\Map\ModuleTableMap;

/**
 * Class RemoveUselessCartEventListeners
 * @package RemoveUselessCart\EventListeners
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class RemoveUselessCartEventListeners implements EventSubscriberInterface
{
    /**
     * Returns an array of event names this subscriber wants to listen to.

     * @return array The event names to listen to
     * @api
     */
    public static function getSubscribedEvents()
    {
        return [
            RemoveUselessCartEvents::REMOVE_USELESS_CARTS => ['remove', 128]
        ];
    }

    /**
     * Propel does not support DELETE with LEFT JOIN, so the function gets
     * carts to remove by range and removes them until there are no more to delete.
     * Do 5 iterations to avoid server timeout
     *
     * @param RemoveUselessCartEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function remove(RemoveUselessCartEvent $event)
    {
        $startDate = $event->getStartDate();

        // If carts with products must not be removed
        if (!$event->getRemoveAll()) {
            $sqlRemoveCarts =
                "DELETE `cart` FROM `cart` LEFT JOIN `cart_item` ON ( `cart`.`ID` = `cart_item`.`CART_ID` ) WHERE `cart`.`UPDATED_AT` <= :startDate AND `cart_item`.`QUANTITY` IS NULL ";
        } else {
            $sqlRemoveCarts =
                "DELETE `cart` FROM `cart` WHERE `cart`.`UPDATED_AT` <= :startDate";
        }

        // Get database connection
        $con = Propel::getWriteConnection(ModuleTableMap::DATABASE_NAME);
        $database = new Database($con);

        // Execute query
        $stmtRemoveCarts = $database->execute($sqlRemoveCarts, [':startDate' => $startDate]);

        // Fill event with number of removed carts
        $event->setRemovedCarts($stmtRemoveCarts->rowCount());
    }
}
