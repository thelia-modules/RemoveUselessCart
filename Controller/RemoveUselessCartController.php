<?php

namespace RemoveUselessCart\Controller;

use RemoveUselessCart\Event\RemoveUselessCartEvent;
use RemoveUselessCart\Event\RemoveUselessCartEvents;
use RemoveUselessCart\Form\RemoveUselessCartForm;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use \Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\HttpFoundation\Session\Session;
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
     * Remove carts with last_update older than the given date
     *
     * @return mixed|RedirectResponse|Response
     */
    #[Route('/admin/module/RemoveUselessCart/remove', name: 'removeuselesscart.remove', methods: ['POST'])]
    public function removeAction(Session $session, EventDispatcherInterface $dispatcher): mixed
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'RemoveUselessCart', AccessManager::DELETE)) {
            return $response;
        }

        $form = $this->createForm(RemoveUselessCartForm::getName());

        try {
            // Validate form
            $vForm = $this->validateForm($form, 'POST');

            // Build event from form data & dispatch it
            $event = new RemoveUselessCartEvent($vForm->getData()['start_date'], $vForm->getData()['remove_all']);
            $dispatcher->dispatch($event, RemoveUselessCartEvents::REMOVE_USELESS_CARTS);

            // Get a number of removed carts
            $session->getFlashBag()->add(
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
            $session->getFlashBag()->add('error', $e->getMessage());

            return $this->generateRedirectFromRoute(
                'admin.module.configure',
                [],
                ['module_code' => 'RemoveUselessCart']
            );
        }
    }
}
