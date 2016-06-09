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

namespace WosClient\Exception;

/**
 * WOS Request Exception
 *
 * Top-level exception for WOS runtime requests
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
abstract class WosException extends \RuntimeException
{
    // Pass..
}
