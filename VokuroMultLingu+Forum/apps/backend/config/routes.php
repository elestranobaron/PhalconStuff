<?php
/*
 * Define custom routes. File gets included in the router service definition.
 */
$router = new Phalcon\Mvc\Router();

$router->add('/confirm/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'confirmEmail'
));

$router->add('/reset-password/{code}/{email}', array(
    'controller' => 'user_control',
    'action' => 'resetPassword'
));

$router->add("/set-language/{language:[a-z]+}", array(
    'controller' => 'index',
    'action' => 'setLanguage'
));
/*
$router->add('/backend/', array(
    'controller' => 'index',
    'action' => 'index'
));
*/
return $router;
