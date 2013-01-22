<?php 

namespace BaseXMS\Log;

class Logger extends \Zend\Log\Logger
{
	public function log( $priority, $message, $extra = array() )
	{
		// add details to $extra
		$trace = debug_backtrace();
		
		$caller = $trace[ 1 ];
		$prevCaller = $trace [ 2 ];
		// simplify $caller
		unset( $caller[ 'object' ], $caller[ 'type' ], $caller[ 'args' ] );
		// overriding function and class from $prevCaller 
		$caller[ 'function' ] = $prevCaller[ 'function' ];
		$caller[ 'class' ] = $prevCaller[ 'class' ];
		
		$extra = array_merge( $extra, $caller );

		if( is_string( $message ) )
		{
			$message = str_replace( '<', '--bigger than--', $message );
		}
		
		return parent::log( $priority, $message, $extra );
	}
}

?>