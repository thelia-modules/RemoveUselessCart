<?php

namespace RemoveUselessCart\Controller;

use Propel\Runtime\ActiveQuery\Criteria;
use Symfony\Component\HttpFoundation\RedirectResponse;
use \Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;
use Thelia\Model\CartQuery;

class RemoveUselessCartController extends BaseAdminController
{
    public function viewConfigAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'RemoveUselessCart', AccessManager::VIEW)) {
            return $response;
        }

        return $this->render("removeuselesscart-configuration", []);
    }

    public function removeAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'RemoveUselessCart', AccessManager::DELETE)) {
            return $response;
        }

        // Validate form and get data
        $form = $this->createForm('removeuselesscart_form');

        try {
            $startDate = $this->validateForm($form, 'POST')->getData()['start_date'];

            CartQuery::create()
                ->filterByUpdatedAt($startDate, Criteria::LESS_EQUAL)
                ->delete();

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