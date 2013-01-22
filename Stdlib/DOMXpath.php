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
}

?>