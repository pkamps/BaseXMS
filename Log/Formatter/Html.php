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
		$output  = '<li><div class="header">';
		$output .= '<span class="level'. $event[ 'priority' ] .'">'. $event[ 'priorityName' ] .'</span> ';
		$output .= '<span class="location" title="'. $event[ 'extra' ][ 'file' ] .'">' . $event[ 'extra' ][ 'class' ] . ' :: ';
		$output .= $event[ 'extra' ][ 'function' ] . ' :: '. $event[ 'extra' ][ 'line' ] .'</span></div>';
		$output .= '<pre>'. $event[ 'message' ] .'</pre></li>';
		
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