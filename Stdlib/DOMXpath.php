<?php 

namespace BaseXMS\Stdlib;

class DOMXPath extends \DOMXPath
{
	/**
	 * Tries to find a xpath of all given xpaths in $tests that matches the Xpath document.
	 * 
	 * @param array $tests
	 * @return Ambigous <boolean, unknown>
	 */
	public function getFirstMatchingXpath( $tests )
	{
		$return = false;
		
		if( !empty( $tests ) )
		{
			foreach( $tests as $xpath => $result )
			{
				if( $this->query( $xpath )->length )
				{
					$return = $result;
					break;
				}
			}
		}
		
		return $return;
	}
	
	
	/**
	 * If the query returns a non-empty NodeList. It will return the value of the first element.
	 * 
	 * @param string $query
	 * @return mixed
	 */
	public function queryToValue( $query, $contextNode = null )
	{
		$return = false;
		
		$result = $this->query( $query, $contextNode );
		
		if( $result->length )
		{
			$firstElement = $result->item(0);

			if( $firstElement instanceof \DOMElement )
			{
				$return = $firstElement->nodeValue;
			}
			else
			{
				$return = $result->item(0)->value;
			}
		}
		
		return $return;
	}
}

?>