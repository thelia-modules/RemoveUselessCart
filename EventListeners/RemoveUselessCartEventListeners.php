<?php

namespace RemoveUselessCart\EventListeners;

use Propel\Runtime\ActiveQuery\Criteria;
use RemoveUselessCart\Event\RemoveUselessCartEvent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Thelia\Model\CartQuery;

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
            'remove-useless-carts' => ['remove', 128]
        ];
    }

    /**
     * Propel does not support DELETE with LEFT JOIN, so the function gets
     * carts to remove by range and removes them until there are no more to delete.
     *
     * @param RemoveUselessCartEvent $event
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function remove(RemoveUselessCartEvent $event)
    {
        $totalRemovedCarts = 0;

        do {
            // Create query filtered by date
            $findCartQuery = CartQuery::create()
                ->filterByUpdatedAt($event->getStartDate(), Criteria::LESS_EQUAL);

            // If carts with products must not be removed
            if (!$event->getRemoveAll()) {
                $findCartQuery
                    ->leftJoinCartItem()
                    ->where('cart_item.QUANTITY IS NULL');
            }

            // Get an array of cart IDs to remove
            $cartList = $findCartQuery
                ->select('ID')
                ->limit(2000)
                ->find()
                ->toArray();

            // Remove carts
            CartQuery::create()
                ->filterById($cartList)
                ->delete();

            // Get number of removed carts
            $totalRemovedCarts += count($cartList);
        } while (!empty($cartList));

        // Set number of removed carts
        $event->setRemovedCarts($totalRemovedCarts);
    }
}
