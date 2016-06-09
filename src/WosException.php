<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/3/16
 * Time: 10:47 AM
 */

namespace WosClient;

/**
 * WOS Exceptions are thrown when the WOS returns error codes
 *
 * Specifically, non-0 x-ddn-status HTTP headers are translated to this
 *
 * @author Casey McLaughlin <caseyamcl@gmail.com>
 */
class WosException extends \RuntimeException
{
    /**
     * Code Names Map
     *
     * @var array|string[]
     */
    private static $codeNames  = [
        200 => 'NoNodeForPolicy',
        201 => 'NoNodeForObject',
        202 => 'UnknownPoilcyName',
        203 => 'InternalError',
        205 => 'InvalidObjId',
        206 => 'NoSpace',
        207 => 'ObjNotFound',
        208 => 'ObjCorrupted',
        209 => 'FsCorrupted',
        210 => 'PolicyNotSupported',
        211 => 'IOErr',
        212 => 'InvalidObjectSize',
        214 => 'TemporarilyNotSupported',
        216 => 'ReservationNotFound',
        217 => 'EmptyObject',
        218 => 'InvalidMetadataKey',
        219 => 'UnusedReservation',
        220 => 'WireCorruption',
        221 => 'CommandTimeout',
        222 => 'InvalidGetSpan'
    ];

    /**
     * Code Meanings Map
     *
     * @var array|string[]
     */
    private static $codeMeanings = [
        200 => 'No nodes will accept Put or Reserve operations for this policy',
        201 => 'No nodes have a copy of the requested object',
        202 => 'Policy name or id is not currently supported by the cluster',
        203 => 'Unknown internal Error',
        205 => 'An invalid OID was specified',
        206 => 'The cluster is full',
        207 => 'Object cannot be located',
        208 => 'Object does not match its checksum',
        209 => 'Filesystem internal structures are corrupted',
        210 => 'Insufficient cluster resources to service Put or Reserve request for this policy',
        211 => 'Unrecoverable drive error',
        212 => '> 5 TB',
        214 => 'Operation should be retried momentarily',
        216 => 'Reservation not found for specified OID',
        217 => 'Attempt to store a zero-length object',
        218 => 'Invalid metadata key specified',
        219 => 'Attempted Get of an unused reservation',
        220 => 'Uncorrectable network corruption of the object',
        221 => 'Command did not complete in a timely manner',
        222 => 'Illegal combination of buffered=true, integity=false'
    ];

    // ---------------------------------------------------------------

    /**
     * WosException constructor.
     *
     * @param string     $code
     * @param string     $message
     * @param \Exception $previous
     */
    public function __construct($code, $message = '', \Exception $previous = null)
    {
        if ($code == 0) {
            throw new \RuntimeException('0 DDN code is not an error code (it is the success code)');
        }
        if (! array_key_exists($code, static::$codeNames)) {
            throw new \RuntimeException("Invalid/unknown DDN error code: " . $code);
        }

        parent::__construct($message ?: static::$codeMeanings[$code], $code, $previous);
    }

    /**
     * Get DDN error name
     *
     * @return string
     */
    public function getErrorName()
    {
        return static::$codeNames[$this->getCode()];
    }

    /**
     * Get the DDN error meaning
     *
     * @return string
     */
    public function getErrorMeaning()
    {
        return static::$codeMeanings[$this->getCode()];
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return sprintf('%s %s: %s', $this->getCode(), $this->getErrorName(), $this->getErrorMeaning());
    }
}
