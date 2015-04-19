<?php

namespace Library;

use Library;
use PHPRouter as Router;

/**
 * Created by PhpStorm.
 * User: Ale
 */
class Application {

    protected $config;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->loadConfig();
    }

    /**
     * Loads config file.
     */
    protected function loadConfig()
    {
        $string = file_get_contents(APPLICATION_PATH . "/config/application.json");
        $this->config = json_decode($string, true);
    }

    /**
     * Do all initializations
     *
     * @return $this
     */
    public function bootstrap()
    {
        // Initializes the database connection
        $this->initDB();

        // Initializes all the routes
        $this->initRoutes();

        // Initializes the session
        $this->initSession();

        return $this;
    }

    /**
     * Starts the execution loop
     */
    public function run()
    {
        /** @var $registry Registry */
        $registry = Registry::getInstance();

        /** @var $router Router\Router */
        $router = $registry->router;
        $router->matchCurrentRequest();

        ob_start();

        echo $registry->response;

        ob_flush();
    }

    /**
     * Initialized all the routes
     */
    protected function initRoutes()
    {
        $collection = new Router\RouteCollection();

        $collection->attachRoute(new Router\Route('/api/users', array(
            '_controller' => 'api\UsersController::index',
            'methods' => 'GET'
        )));

        $collection->attachRoute(new Router\Route('/api/', array(
            //@todo
            '_controller' => 'api\UsersController::index',
            'methods' => 'GET'
        )));

        // catch all other routes to be routed with angular
        $collection->attachRoute(new Router\Route('/', array(
            '_controller' => 'controllers\HomeController::index',
            'methods' => 'GET'
        )));

        $router = new Router\Router($collection);
        $router->setBasePath('');

        // Register router
        Registry::getInstance()->router = $router;
    }

    /**
     * Loads the database config and opens the connection
     */
    protected function initDB()
    {
        $db = new Library\Database($this->config['database']);
        Registry::getInstance()->db = $db;
    }

    /**
     * Starts the session
     */
    function initSession()
    {
        session_cache_expire(0);
        session_start();
    }
}