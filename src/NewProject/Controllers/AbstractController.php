<?php

namespace NewProject\Controllers;

use NewProject\Models\Users\User;
use NewProject\Services\UserAuthService;
use NewProject\View\View;

abstract class AbstractController
{
    protected $view;
    protected $user;

    public function __construct()
    {
        $this->user = UserAuthService::getUserByToken();
        $this->view = new View(__DIR__ . '/../../../templates');
        $this->view->setVar('user', $this->user);
    }
}
