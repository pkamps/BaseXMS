<?php 

namespace BaseXMS\Stdlib;

class DOMXpath extends \DOMXpath
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
	public function queryToValue( $query )
	{
		$return = false;
		
		$result = $this->query( $query );
		
		if( $result->length )
		{
			$return = $result->item(0)->value;
		}
		
		return $return;
	}
}

?>