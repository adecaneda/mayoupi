<?php

namespace Library;


class Controller {

    /**
     * Stores the response to be output as a json object.
     *
     * @param $data
     */
    protected function json($data)
    {
        Registry::getInstance()->response = json_encode($data);
    }

    /**
     * Stores the response to be output as a rendered template.
     *
     * @param $data
     * @param $file
     */
    protected function html($data, $file)
    {
        /** @var $registry Registry */
        $registry = Registry::getInstance();

        /** @var $view View */
        $view = View::getInstance();
        $view->setBasePath(APPLICATION_PATH . '\\views');

        $view->setData($data);
        $registry->response = $view->output($file);
    }
} 