<?php

namespace Library;


class Controller {

    /**
     * Stores the response to be output as a json object.
     *
     * @param $data
     * @param $status
     */
    protected function json($data, $status = 200)
    {
        $response = new HTTPResponse($data, $status);
        Registry::getInstance()->response = $response;
    }

    /**
     * Stores the response to be output as a rendered template.
     *
     * @param $data
     * @param $file
     * @param $status
     */
    protected function html($data, $file, $status = 200)
    {
        /** @var $registry Registry */
        $registry = Registry::getInstance();

        /** @var $view View */
        $view = View::getInstance();
        $view->setBasePath(APPLICATION_PATH . '\\views');

        $view->setData($data);

        $response = new HTTPResponse($view->output($file), $status, 'html');
        $registry->response = $response;
    }

    /**
     * @param $name
     *
     * @return null
     */
    protected function getPost($name)
    {
        if (isset($_POST[$name])) {
            return $_POST[$name];
        }
        return null;
    }

}