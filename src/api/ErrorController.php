<?php

namespace api;

use Library\Controller;

class ErrorController extends Controller{

    public function index()
    {
        $this->json(array(), 404);
    }
} 