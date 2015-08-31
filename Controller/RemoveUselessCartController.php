<?php

namespace RemoveUselessCart\Controller;

use \Thelia\Controller\Admin\BaseAdminController;
use Thelia\Core\Security\AccessManager;
use Thelia\Core\Security\Resource\AdminResources;

class RemoveUselessCartController extends BaseAdminController
{
    public function viewConfigAction()
    {
        if (null !== $response = $this->checkAuth(array(AdminResources::MODULE), 'RemoveUselessCart', AccessManager::VIEW)) {
            return $response;
        }

        return $this->render("removeuselesscart-configuration", []);
    }
}