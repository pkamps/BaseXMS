<?php
/**
 */

namespace BaseXMS\Cache\Storage\Adapter;

use BaseXMS\Cache\Storage\ClearByXPathInterface;

class Filesystem extends \Zend\Cache\Storage\Adapter\Filesystem implements ClearByXPathInterface
{
		
	/* ClearByXPathInterface  */

    /**
     * Set tags to an item by given key.
     * An empty array will remove all tags.
     *
     * @param string   $key
     * @param string[] $tags
     * @return bool
     */
    public function setXml($key, \DOMDocument $doc )
    {
        $this->normalizeKey($key);
        if (!$this->internalHasItem($key)) {
            return false;
        }

        $filespec = $this->getFileSpec($key);

        $index = $this->readIndexFile();
        
        $cacheElement = $index->getElementById( $filespec );
        
        /* Delete keys
        if (!$doc) {
            $this->unlink($filespec . '.xml');
            return true;
        }
		*/
        
        $newCacheElement = '<c id="' . $filespec . '">' . $doc->saveXML( $doc->firstChild ) . '</c>';
        
        $fragment = $index->createDocumentFragment();
        $fragment->appendXML( $newCacheElement );
        
        if( $cacheElement )
        {
        		$cacheElement->parentNode->replaceChild( $newCacheElement , $cacheElement );
        }
        else
        {
    			$index->firstChild->appendChild( $fragment );
        }
        
        $this->writeIndexFile( $index );
        
        return true;
    }

    /**
     * Get tags of an item by given key
     *
     * @param string $key
     * @return string[]|FALSE
     */
    public function getXml($key)
    {
        $this->normalizeKey($key);
        if (!$this->internalHasItem($key)) {
            return false;
        }

        $filespec = $this->getFileSpec($key);
        $doc = new \DOMDocument();
        if (file_exists($filespec . '.xml')) {
            $doc = $doc->loadXML( $this->getFileContent( $filespec . '.xml' ) );
        }

        return $doc;
    }

    /**
     * Remove items matching given tags.
     *
     * If $disjunction only one of the given tags must match
     * else all given tags must match.
     *
     * @param string[] $tags
     * @param  bool  $disjunction
     * @return bool
     */
    public function clearByXPath( $xpath )
    {
        if (!$tags) {
            return true;
        }

        $tagCount  = count($tags);
        $options   = $this->getOptions();
        $namespace = $options->getNamespace();
        $prefix    = ($namespace === '') ? '' : $namespace . $options->getNamespaceSeparator();

        $flags = GlobIterator::SKIP_DOTS | GlobIterator::CURRENT_AS_PATHNAME;
        $path  = $options->getCacheDir()
            . str_repeat(\DIRECTORY_SEPARATOR . $prefix . '*', $options->getDirLevel())
            . \DIRECTORY_SEPARATOR . $prefix . '*.tag';
        $glob = new GlobIterator($path, $flags);

        foreach ($glob as $pathname) {
            $diff = array_diff($tags, explode("\n", $this->getFileContent($pathname)));

            $rem  = false;
            if ($disjunction && count($diff) < $tagCount) {
                $rem = true;
            } elseif (!$disjunction && !$diff) {
                $rem = true;
            }

            if ($rem) {
                unlink($pathname);

                $datPathname = substr($pathname, 0, -4) . '.dat';
                if (file_exists($datPathname)) {
                    unlink($datPathname);
                }
            }
        }

        return true;
    }
    
	private function readIndexFile()
	{
		$filename = $this->getOptions()->getCacheDir() . \DIRECTORY_SEPARATOR . 'index.xml';
		
		if( !file_exists( $filename ) )
		{
			file_put_contents( $filename, '<all></all>' );
		}
		
		$doc = new \DOMDocument();
		$doc->load( $filename );
		
		return $doc;
	}
	
	private function writeIndexFile( $doc )
	{
		$filename = $this->getOptions()->getCacheDir() . \DIRECTORY_SEPARATOR . 'index.xml';
		$doc->save( $filename );
	}
}
