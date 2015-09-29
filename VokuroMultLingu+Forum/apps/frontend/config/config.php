<?php

/*
 +------------------------------------------------------------------------+
 | Phosphorum                                                             |
 +------------------------------------------------------------------------+
 | Copyright (c) 2013-2014 Phalcon Team and contributors                  |
 +------------------------------------------------------------------------+
 | This source file is subject to the New BSD License that is bundled     |
 | with this package in the file docs/LICENSE.txt.                        |
 |                                                                        |
 | If you did not receive a copy of the license and are unable to         |
 | obtain it through the world-wide-web, please send an email             |
 | to license@phalconphp.com so we can send you a copy immediately.       |
 +------------------------------------------------------------------------+
*/

return new \Phalcon\Config(array(

    'site' => array(
        'name'      => 'Phalcon Framework',
        'url'       => 'http://forum.phalconphp.com',
        'project'   => 'Phalcon',
        'software'  => 'Phosphorum',
        'repo'      => 'https://github.com/phalcon/cphalcon/issues',
        'docs'      => 'https://github.com/phalcon/docs',
    ),

    'database'    => array(
        'adapter'  => 'Mysql',
        'host'     => 'localhost',
        'username' => 'root',
        'password' => 'dbpass',
        'dbname'   => 'forum',
        'charset'  => 'utf8'
    ),

    'application' => array(
        'controllersDir' => '/var/www/vokuro/apps/frontend/controllers/',//APP_PATH . '/frontend/controllers/',
        'modelsDir'      => '/var/www/vokuro/apps/frontend/models/',//APP_PATH . '/frontend/models/',
        'viewsDir'       => '/var/www/vokuro/apps/frontend/views/',//APP_PATH . '/frontend/views/',
        'pluginsDir'     => '/var/www/vokuro/apps/frontend/plugins/',//APP_PATH . '/frontend/plugins/',
        'libraryDir'     => '/var/www/vokuro/apps/frontend/library/',//APP_PATH . '/frontend/library/',
        'formsDir'     => '/var/www/vokuro/apps/frontend/forms/',//APP_PATH . '/frontend/forms/',
        'development'    => array(
            'staticBaseUri' => '/',
            'baseUri'       => '/frontend/'
        ),
        'production'     => array(
            'staticBaseUri' => 'http://static.phosphorum.com/',
            'baseUri'       => '/'
        ),
        'debug'          => true
    ),

    'mandrillapp' => array(
        'secret' => ''
    ),

    'github'      => array(
        'clientId'     => '00',
        'clientSecret' => '7',
        'redirectUri'  => 'http://localhost:60/frontend/login/oauth/access_token/',//'http://pforum.loc/login/oauth/access_token/'
    ),

    'amazonSns'   => array(
        'secret' => ''
    ),

    'smtp'        => array(
        'host'     => "localhost",//"",
        'port'     => 25,
        'security' => "",//"tls",
        'username' => "",
        'password' => ""
    ),

    'beanstalk'   => array(
        'disabled' => false,//true,
        'host'     => '127.0.0.1'
    ),

    'elasticsearch' => array(
        'index'    => 'phosphorum'
    ),
    
    'mail'     => array(
        'fromName'     => 'Phalcon',
        'fromEmail'    => 'mail@gmail.com',//'phosphorum@phalconphp.com',
    )
));
