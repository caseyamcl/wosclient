#!/usr/bin/env php
<?php

/**
 * Test a live WOS Server
 *
 * This file bootstraps the command to run a test against a live WOS server
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */

use WosClient\TestLiveServer\LiveServerTestApp;

require_once(__DIR__ . '/../../vendor/autoload.php');

$app = new LiveServerTestApp('WOS Test');
$app->run();
