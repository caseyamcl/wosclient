<?php
/**
 * Created by PhpStorm.
 * User: casey
 * Date: 6/9/16
 * Time: 12:55 PM
 */

namespace WosClient\Helper;


trait ArrayToMetadataStringTrait
{
    /**
     * Prepare metadata for DDN header
     *
     * Converts array into JSON, and removes surrounding brackets
     * Returns an empty string for an empty array
     *
     * @param array $meta
     * @return string
     */
    protected function metadataToString(array $meta = [])
    {
        return preg_replace(
            '/^\{(.+?)?\}$/', '$1',
            json_encode($meta, JSON_FORCE_OBJECT)
        );
    }
}
