<?php 

namespace BaseXMS\Log;

class Logger extends \Zend\Log\Logger
{
	public function say( $message, $extra = array() )
	{
		
		
	}
	
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
		
		return parent::log( Logger::INFO, $message, $extra );
	}
}

?>