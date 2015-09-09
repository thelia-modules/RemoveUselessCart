<?php

namespace RemoveUselessCart\Controller;

use RemoveUselessCart\Event\RemoveUselessCartEvent;
use RemoveUselessCart\Event\RemoveUselessCartEvents;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Core\Translation\Translator;

/**
 * Class RemoveUselessCartController
 * @package RemoveUselessCart\Controller
 * @author Etienne Perriere - OpenStudio <eperriere@openstudio.fr>
 */
class RemoveUselessCartController extends BaseAdminController
{
    /**
     * @return mixed|\Thelia\Core\HttpFoundation\Response
     */
    public function viewConfigAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'RemoveUselessCart', AccessManager::VIEW)) {
            return $response;
        }

        return $this->render("removeuselesscart-configuration", []);
    }

    /**
     * Remove carts with last_update older than the given date
     *
     * @return mixed|RedirectResponse|\Thelia\Core\HttpFoundation\Response
     */
    public function removeAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'RemoveUselessCart', AccessManager::DELETE)) {
            return $response;
        }

        $form = $this->createForm('removeuselesscart_form');

        try {
            // Validate form
            $vForm = $this->validateForm($form, 'POST');

            // Build event from form data & dispatch it
            $event = new RemoveUselessCartEvent($vForm->getData()['start_date'], $vForm->getData()['remove_all']);
            $this->getDispatcher()->dispatch(RemoveUselessCartEvents::REMOVE_USELESS_CARTS, $event);

            // Get number of removed carts
            $this->getSession()->getFlashBag()->add(
                'remove-cart-result',
                Translator::getInstance()->trans(
                    'Successfully removed %nbCarts carts!',
                    ['%nbCarts' => $event->getRemovedCarts()],
                    'removeuselesscart'
                )
            );

            // Redirect
            return new RedirectResponse($form->getSuccessUrl());
        } catch (\Exception $e) {
            $this->setupFormErrorContext(
                null,
                $e->getMessage(),
                $form
            );

            return $this->render('removeuselesscart-configuration', []);
        }
    }
}
