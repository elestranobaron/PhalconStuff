<?php
return new \Phalcon\Config(array(
    'database' => array(
        'adapter' => 'Mysql',
        'host' => '127.0.0.1',
        'username' => 'root',
        'password' => 'dbpass',
        'dbname' => 'vokuro'
    ),
    'application' => array(
        'controllersDir' => APP_DIR . '/controllers/',
        'modelsDir' => APP_DIR . '/models/',
        'formsDir' => APP_DIR . '/forms/',
        'viewsDir' => APP_DIR . '/views/',
        'libraryDir' => APP_DIR . '/library/',
        'pluginsDir' => APP_DIR . '/plugins/',
        'cacheDir' => APP_DIR . '/cache/',
        'baseUri' => '/',
        'publicUrl' => 'vokuro.phalconphp.com',
        'cryptSalt' => 'eEAfR|_&G&f,+vU]:jFr!!A&+71w1Ms9~8_4L!<@[N@DyaIP_2My|:+.u>/6m,$D'
    ),
    'mail' => array(
        'fromName' => 'Vokuro',
        'fromEmail' => 'mail@gmail.com',
        'smtp' => array(
            'server' => 'smtp.gmail.com',//'smtp-mail.outlook.com',
            'port' => 587,
            'security' => 'tls',
            'username' => 'mail@gmail.com',
            'password' => 'pass'
        )
    ),
    'amazon' => array(
        'AWSAccessKeyId' => 'amazonAccess',
        'AWSSecretKey' => 'amazonKey'
    )
));
