<?php
/**
 */

namespace BaseXMS\Cache\Storage;

use BaseXMS\Stdlib\DOMDocument;

interface ClearByXPathInterface
{
    /**
     * Set tags to an item by given key.
     * An empty array will remove all tags.
     *
     * @param string      $key
     * @param DOMDocument $doc
     * @return bool
     */
    public function setXml( $key, \DOMDocument $doc );

    /**
     * Get tags of an item by given key
     *
     * @param string $key
     * @return DOMDocument|FALSE
     */
    public function getTags( $key );

    /**
     * Remove items matching given tags.
     *
     * If $disjunction only one of the given tags must match
     * else all given tags must match.
     *
     * @param  bool  $xpath
     * @return bool
     */
    public function clearByXPath( $xpath );
}
