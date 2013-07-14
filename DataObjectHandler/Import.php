<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\DataObjectHandler\DataObjectHandler;
use BaseXMS\DataObjectHandler\Node;

class Import extends DataObjectHandler
{

	public function importNode( $node, $parentId )
	{
		
	}
	
	/**
	 * @param \DOMDocument $doc
	 * @param string $parentId
	 * @return boolean
	 */
	public function importNodeTree( \DOMDocument $doc, $parentId )
	{
		$return = false;
		
		if( $doc instanceof \DOMDocument )
		{
			$nodeHandler = new Node();
			$nodeHandler->setServiceLocator( $this->getServiceLocator() );
			
			$parentNode = $nodeHandler->read( $parentId );
			
			if( $parentNode )
			{
				$query = 'insert node '. $doc->saveXML( $doc->firstChild ) .
				         'as last into //node[@id="' . $parentId . '"]';
					
				$result = $this->services->get( 'xmldb' )->execute( $query );
				
				if( !is_null( $result ) )
				{
					$return = true;
				}
			}
		}
				
		return $return;
	}
	
}

?>