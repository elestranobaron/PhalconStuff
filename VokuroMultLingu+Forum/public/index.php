<?php

// public/index.php

error_reporting(E_ALL);

class Application extends \Phalcon\Mvc\Application
{
    private static $mode    = 'dev';
    private static $modules;

    const DEFAULT_MODULE    = 'backend';

    const MODE_PRODUCTION   = 'prod';
    const MODE_STAGING      = 'staging';
    const MODE_TEST         = 'test';
    const MODE_DEVELOPMENT  = 'dev';

    /**
     * Set application mode and error reporting level.
     */
    public function __construct()
    {
        //@todo Move this !
        static::$modules = array(
            'frontend' => array(
                    'className' => 'Phosphorum\Module',//'Test\Frontend\Module',
                    'path' => __DIR__ . '/../apps/frontend/Module.php'
            ),
            'backend' => array(
                    'className' => 'Test\Backend\Module',
                    'path' => __DIR__ . '/../apps/backend/Module.php'
            ),
        );

        if (!defined('PHALCON_MODE')) {
            $mode = getenv('PHALCON_MODE');
            $mode = $mode ? $mode : self::$mode;
            define('PHALCON_MODE', $mode);
        }

        switch (self::getMode()) {
            case self::MODE_PRODUCTION:
            case self::MODE_STAGING:
                error_reporting(0);
                break;

            case self::MODE_TEST:
            case self::MODE_DEVELOPMENT:
                error_reporting(E_ALL);
                break;
        }
    }

    /**
     * Register the services here to make them general or register in
     * the ModuleDefinition to make them module-specific.
     */
    protected function _registerServices()
    {
        $loader = new \Phalcon\Loader();

        /**
         * We're a registering a set of directories taken from the configuration file
        */
        /*
        $loader->registerDirs(
                array(
                        __DIR__ . '/../library/',
                        __DIR__ . '/../vendor/',
                )
        )->register();
        */

        // Init a DI
        $di = new \Phalcon\DI\FactoryDefault();

        // Registering a router:
        $defaultModule = self::DEFAULT_MODULE;
        $modules = self::$modules;
        $di->set('router', function() use ($defaultModule, $modules) {

            $router = new \Phalcon\Mvc\Router();

            $router->setDefaultModule($defaultModule);

            foreach ($modules as $moduleName => $module) {

                // do not route default module
                if ($defaultModule == $moduleName) {
                    continue;
                }

                $router->add('#^/'.$moduleName.'(|/)$#', array(
                    'module' => $moduleName,
                    'controller' => 'index',
                    'action' => 'index',
                ));

                $router->add('#^/'.$moduleName.'/([a-zA-Z0-9\_]+)[/]{0,1}$#', array(
                    'module' => $moduleName,
                    'controller' => 1,
                ));

                $router->add('#^/'.$moduleName.'[/]{0,1}([a-zA-Z0-9\_]+)/([a-zA-Z0-9\_]+)(/.*)*$#', array(
                    'module' => $moduleName,
                    'controller' => 1,
                    'action' => 2,
                    'params' => 3,
                ));

		$router->add("/set-language/{language:[a-z]+}", array(
    			'controller' => 'index',
    			'action' => 'setLanguage'
		));						//Module default

		$router->add(
    			'#^/'.$moduleName.'/activity#',		// Module non default . . .
    			array(
			'module'    => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'activity'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/activity/irc#',
    			array(
			'module'    => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'irc'
    			)
		);
		$router->add(
   		 	'#^/'.$moduleName.'/help/create-post#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'help',
       			'action'     => 'create'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/search#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'search'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/login/oauth/authorize#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'session',
       			'action'     => 'authorize'
   	 		)
		);
		$router->add(
    			'#^/'.$moduleName.'/login/oauth/access_token/#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'session',
       			'action'     => 'accessToken'
    			)
		);
/*		$router->add(
    			'#^/'.$moduleName.'/login/oauth/access_token#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'session',
       			'action'     => 'accessToken'
    			)
		);*/
		$router->add(
    			'#^/'.$moduleName.'/settings#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'settings'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/settings/advanced#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'advanced'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/settings/newcat#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'newcat'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/settings/editcat/{id:[0-9]+}',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'editcat'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/settings/deletecat/{id:[0-9]+}',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'deletecat'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/logout#',
    			array(
				'module'     => $moduleName,
       				'controller' => 'session',
       				'action'     => 'logout'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/notifications#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'notifications'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/post/discussion#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'create'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/sitemap#',
   	 		array(
				'module'     => $moduleName,
       				'controller' => 'sitemap',
       				'action'     => 'index'
    			)
		);
		$router->addPost(
    			'#^/'.$moduleName.'/preview#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'utils',
       			'action'     => 'preview'
    			)
		);
		$router->add(//call discretment par js
    		'#^/'.$moduleName.'/reload-categories#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'reloadCategories'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/category/{id:[0-9]+}/{slug}/{offset:[0-9]+}$',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'category'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/category/{id:[0-9]+}/{slug}$',
    			array(
				'module'     => $moduleName,
       				'controller' => 'discussions',
       				'action'     => 'category'
    			)
		);
		$router->add(
    			'/frontend/',
		//	'#^/'.$moduleName.'/#', // proscrire	
    			array(
			'module'     => $moduleName,
       			'controller' => 'categories',
       			'action'     => 'index'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/discussion/{id:[0-9]+}/{slug}$',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'view'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/discussions/{order:[a-z]+}$',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'index'
    			)
			);

		$router->add(
    		'^/'.$moduleName.'/discussions/{order:[a-z]+}/{offset:[0-9]+}$',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'index'
    		)	
		);
		$router->add(
    			'^/'.$moduleName.'/user/{id:[0-9]+}/{login}$',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'user'
    			)
		);
		$router->add(
    			'^/'.$moduleName.'/discussions$',
    			array(
			'module'     => $moduleName,
       			'controller' => 'discussions',
       			'action'     => 'index'
    			)
		);
		$router->add(
    			'#^/'.$moduleName.'/index.html#',
    			array(
			'module'     => $moduleName,
       			'controller' => 'categories',
       			'action'     => 'index'
    			)
		);
		$router->add(
				'^/'.$moduleName.'/reply/accept/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'replies',
					'action'     => 'accept'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/reply/vote-up/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'replies',
					'action'     => 'voteUp'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/reply/vote-down/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'replies',
					'action'     => 'voteDown'
				     )
			    );

		$router->add(
				'#^/'.$moduleName.'/reply/history/{id:[0-9]+}#',
				array(
			'module'     => $moduleName,
					'controller' => 'replies',
					'action'     => 'history'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/discussion/history/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'history'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/discussion/vote-up/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'voteUp'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/discussion/vote-down/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'voteDown'
				     )
			    );
		$router->add(
				'^/'.$moduleName.'/edit/discussion/{id:[0-9]+}$',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'edit'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/subscribe/discussion/{id:[0-9]+}',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'subscribe'
				     )
			    );

		$router->add(
				'^/'.$moduleName.'/unsubscribe/discussion/{id:[0-9]+}',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'unsubscribe'
				     )
			    );
		$router->add(
				'^/'.$moduleName.'/delete/discussion/{id:[0-9]+}',
				array(
			'module'     => $moduleName,
					'controller' => 'discussions',
					'action'     => 'delete'
				     )
			    );
		$router->add(
				'^/'.$moduleName.'/reply/delete/{id:[0-9]+}',
				array(
			'module'     => $moduleName,
					'controller' => 'replies',
					'action'     => 'delete'
				     )
			    );
            }

            return $router;
        });

        /**
         * The URL component is used to generate all kind of urls in the application
         */
        $di['url'] = function() {
            $url = new \Phalcon\Mvc\Url();
            $url->setBaseUri('/');
            return $url;
        };

        /**
         * Start the session the first time some component request the session service
         */
        $di['session'] = function() {
            $session = new \Phalcon\Session\Adapter\Files();
            $session->start();
            return $session;
        };

        $this->setDI($di);
    }

    public function main()
    {
        try {
            $this->registerModules(self::$modules);
            $this->_registerServices();

            echo $this->handle()->getContent();

        } catch (Exception $e) {

            /*$logger = new \Phalcon\Logger\Adapter\File(__DIR__ . '/../logs/'.date('Y-m-d').'.log');
            $logger->log($e->getMessage(), \Phalcon\Logger::ERROR);

            // remove view contents from buffer
            ob_clean();

            $errorCode = 500;
            $errorView = 'errors/500.html';

            switch (true) {
                // 401 UNAUTHORIZED
                case $e->getCode() == 401:
                    $errorCode = 401;
                    $errorView = 'errors/401.html';
                    break;

                    // 403 FORBIDDEN
                case $e->getCode() == 403:
                    $errorCode = 403;
                    $errorView = 'errors/403.html';
                    break;

                    // 404 NOT FOUND
                case $e->getCode() == 404:
                case ($e instanceof Phalcon\Mvc\View\Exception):
                case ($e instanceof Phalcon\Mvc\Dispatcher\Exception):
                    $errorCode = 404;
                    $errorView = 'errors/404.html';
                    break;
            }

            // Get error view contents. Since we are including the view
            // file here you can use PHP and local vars inside the error view.
            ob_start();
            include_once $errorView;
            $contents = ob_get_contents();
            ob_end_clean();

            // send view to header
            $response = $this->getDI()->getShared('response');
            $response->resetHeaders()
                ->setStatusCode($errorCode, null)
                ->setContent($contents)
                ->send();
            */
            echo $e->getMessage();
        }
    }

    public static function getMode()
    {
        return self::$mode;
    }
}

// Run application:
$app = new Application();
$app->main();
