<?php
/**
 * @namespace
 */
namespace BaseXMS\Log;

use Zend\Log\Filter\FilterInterface;

class Filter implements FilterInterface
{
    public function __construct()
    {
    }

	public function filter( array $event )
	{
		$return = true;

		$filterDefinitions = $this->getFitlerDefinitions();
		
		if( !empty( $filterDefinitions ) )
		{
			$return = false;
			
			// flatten values
			if( !empty( $event[ 'extra' ] ) )
			{
				foreach( $event[ 'extra' ] as $key => $value )
				{
					$event[ $key ] = $value;
				}
			}
			unset( $event[ 'extra' ] );
			
			
			foreach( $filterDefinitions as $entry )
			{
				if( count( array_intersect( $entry, $event ) ) == count( $entry ) )
				{
					$return = true;
					break;
				}
			}
		}
				
		return $return;
	}
	
	private function getFitlerDefinitions()
	{
		return array( array( 'class' => 'BaseXMS\Log\Logger' ) );
	}
}
