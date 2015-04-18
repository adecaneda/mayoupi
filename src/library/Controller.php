<?php

namespace Library;


class Controller {

    protected function json($data)
    {
        Registry::getInstance()->response = json_encode($data);
    }
} 