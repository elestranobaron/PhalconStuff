<?php

//frontend/Module.php

namespace Phosphorum;//Test\Frontend;

use Phalcon\Loader,
	Phalcon\Mvc\View,
	Phalcon\Mvc\Dispatcher,
	Phalcon\Db\Adapter\Pdo\Mysql as DbAdapter,
	Phalcon\DI\FactoryDefault,
	Phalcon\Mvc\ModuleDefinitionInterface;

class Module implements ModuleDefinitionInterface
{

	/**
	 * Registers the module auto-loader
	 */
	public function registerAutoloaders(\Phalcon\DiInterface $dependencyInjector = NULL)
	{
//		if (!isset($_GET['_url'])) {
//			$_GET['_url'] = '/';
//		}
		define('BASE_DIR', dirname(__DIR__));
		define('APP_DIR', BASE_DIR . '/frontend');

//		$loader = new Loader();
//		require "/var/www/vokuro/apps/frontend/noutility/vendor/autoload.php";
		$config = include __DIR__ . "/config/config.php";
		include __DIR__ . "/config/loader.php";
		include __DIR__ . "/noutility/vendor/autoload.php";

//		$loader->registerNamespaces(array(
//			'Test\Frontend\Controllers' => __DIR__ . '/controllers/',
//			'Test\Frontend\Models' => __DIR__ . '/models/',
//		));

		$loader->register();
	}

	/**
	 * Registers the module-only services
	 *
	 * @param Phalcon\DI $di
	 */
	public function registerServices(\Phalcon\DiInterface $di)//$dependencyInjector = NULL)//$di)
	{
		/**
		 * Read configuration
		 */
//		define('BASE_DIR', dirname(__DIR__));
//		define('APP_DIR', BASE_DIR . '/frontend');

//		$di = new FactoryDefault();
//		$view = new View();
		$config = include __DIR__ . "/config/config.php";////////////////////////////////////////////////////////>>>>>>>>>>
		$di['dispatcher'] = function() {
		    $dispatcher = new Dispatcher();
		    $dispatcher->setDefaultNamespace("Phosphorum\Controllers");
		    return $dispatcher;
		};
		//Register Volt as a service
		$di['voltService'] = function($view, $di) {
		    $volt = new \Phalcon\Mvc\View\Engine\Volt($view, $di);

		    $volt->setOptions(array(
		            "compiledPath" => "../cache/compiled-templates/",
		            "compiledExtension" => ".compiled",
		            "compileAlways" => true
		    ));

		    return $volt;
		};

		/**
		 * Setting up the view component
		 */
		$di['view'] = function() {
			$view = new View();
			$view->setViewsDir(__DIR__ . '/views/');//default/');
	//		print(__DIR__);
			$view->registerEngines(array(
			    ".volt" => 'voltService'
			));
			return $view;
		};
		include __DIR__ . "/config/services.php";

		/**
		 * Database connection is created based in the parameters defined in the configuration file
		 */
		$di['db'] = function() use ($config) {
			return new DbAdapter(array(
				"host" => $config->database->host,
				"username" => $config->database->username,
				"password" => $config->database->password,
				"dbname" => $config->database->dbname
			));
		};

	}

}
