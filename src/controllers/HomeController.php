<?php

namespace controllers;

use Library\Controller;

class HomeController extends Controller {

    public function index()
    {
        $this->html(array('variable' => 'asdfasdf'), 'home.phtml');
    }
} 