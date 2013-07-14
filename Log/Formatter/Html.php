<?php

namespace BaseXMS\Log\Formatter;

use Zend\Log\Formatter\FormatterInterface;

class Html implements FormatterInterface
{

	/**
	 * Formats data into a single line to be written by the writer.
	 *
	 * @param  array	$event  event data
	 * @return string       formatted line to write to the log
	 */
	public function format( $event )
	{
		$priorityCssMap = array(
				1 => 'btn-warning',
				2 => 'btn-warning',
				3 => 'btn-warning',
				4 => 'btn-warning',
				5 => 'btn-success',
				6 => 'btn-primary',
				7 => 'btn-info' );
		
		$cssClass = $priorityCssMap[ $event[ 'priority' ] ];
		
		/*
		 * A bit of a hack: PHP errors give a "context" - BaseXMS log events don't.
		 * I use it to determen if the event comes from php
		 */ 
		
		$fromPHP = isset( $event[ 'extra' ][ 'context' ] );
		
		$output  = '<div class="header">';
		if( $fromPHP )
		{
			$output .= '<span class="btn btn-inverse">PHP</span> ';
		}
		$output .= '<span class="btn '. $cssClass .'">'. $event[ 'priorityName' ] .'</span> ';
		$output .= '<span class="location" title="'. $event[ 'extra' ][ 'file' ] .'">' . $event[ 'extra' ][ 'class' ] . ' :: ';
		$output .= $event[ 'extra' ][ 'function' ] . ' :: '. $event[ 'extra' ][ 'line' ] .'</span></div>';
		$output .= '<pre>'. $event[ 'message' ] .'</pre>';
		
		//$output .= print_r( $event, true );
		return $output;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function getDateTimeFormat()
	{
		return $this->dateTimeFormat;
	}
	
	/**
	 * {@inheritDoc}
	 */
	public function setDateTimeFormat($dateTimeFormat)
	{
		$this->dateTimeFormat = (string) $dateTimeFormat;
		return $this;
	}
}

?>