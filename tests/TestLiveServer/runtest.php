#!/usr/bin/env php
<?php

/**
 * PHP Client for DDN Web Object Scalar (WOS) API
 *
 * @package Wosclient
 * @author  Casey McLaughlin <caseyamcl@gmail.com>
 * @license http://opensource.org/licenses/MIT MIT
 * @link    https://github.com/caseyamcl/wosclient
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * ------------------------------------------------------------------
 */

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
