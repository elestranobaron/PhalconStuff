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


/**
 * This scripts generates random posts
 */
require 'cli-bootstrap.php';
include ('/var/www/forum/app/library/Mail/SendSpool.php');
use Phosphorum\Mail\SendSpool;

class SendSpoolConsumerTask extends Phalcon\DI\Injectable
{

    public function run()
    {
        $spool = new SendSpool();
        $spool->consumeQueue();
    }
}

try {
    $task = new SendSpoolConsumerTask($config);
    $task->run();
} catch (Exception $e) {
    echo $e->getMessage(), PHP_EOL;
    echo $e->getTraceAsString();
}
