<?php 

namespace BaseXMS\Debug;

class Accumulator
{
	private $data;
	private $memoryUsage;

	/**
	 * @param string $identifier
	 * @param float $microtime
	 */
	public function start( $identifier, $microtime = null )
	{
		$microtime = $microtime ? $microtime : microtime( true );

		$this->data[ $identifier ][ 'start' ][] = $microtime;
		
		if( !isset( $this->data[ $identifier ][ 'accumulation' ] ) )
		{
			$this->data[ $identifier ][ 'accumulation' ] = 0;
		}
	}
	
	public function stop( $identifier, $microtime = null )
	{
		$microtime = $microtime ? $microtime : microtime( true );
		
		if( !empty( $this->data[ $identifier ][ 'start' ] ) )
		{
			$start = array_pop( $this->data[ $identifier ][ 'start' ] );
			$diff = $microtime - $start;
			
			$this->data[ $identifier ][ 'accumulation' ] += $diff;
		}
	}
	
	public function memory_usage( $identifier )
	{
		$this->memoryUsage[ $identifier ] = memory_get_usage();
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	public function getDataAsHtml()
	{
		$return = '';
		
		if( !empty( $this->data ) )
		{
			$return .= '<table border="1">';
			
			foreach( $this->data as $identifier => $entry )
			{
				$return .= '<tr>';
				
				$return .= '<td>' . $identifier . '</td>';
				$return .= '<td>' . number_format( ( $entry[ 'accumulation' ] ) * 1000, 4 ) . ' ms' . '</td>';
				
				$return .= '</tr>';
			}
			
			$return .= '</table>';
		}

		if( !empty( $this->memoryUsage ) )
		{
			$this->memoryUsage[ 'peak' ] = memory_get_peak_usage();
			
			$return .= '<table border="1">';
				
			foreach( $this->memoryUsage as $identifier => $value )
			{
				$unit = array('b','kb','mb','gb','tb','pb');
				
				$return .= '<tr>';
		
				$return .= '<td>' . $identifier . '</td>';
				$return .= '<td>' . round( $value / pow( 1024,( $i = floor( log( $value, 1024 ) ) ) ), 2 ).' '.$unit[$i] . '</td>';
		
				$return .= '</tr>';
			}
				
			$return .= '</table>';
		}
		
		return $return;
	}
	
	public function getDataAsText()
	{
		$return = '';
		
		if( !empty( $this->data ) )
		{
			foreach( $this->data as $identifier => $entry )
			{
				$return .= $identifier . "\t";
				$return .= number_format( ( $entry[ 'accumulation' ] ) * 1000, 4 ) . ' ms' . "\n";
			}
		}

		$return .= "\n";
		
		if( !empty( $this->memoryUsage ) )
		{
			$this->memoryUsage[ 'peak' ] = memory_get_peak_usage();
				
			foreach( $this->memoryUsage as $identifier => $value )
			{
				$unit = array('b','kb','mb','gb','tb','pb');
		
				$return .= $identifier . "\n";
				$return .= round( $value / pow( 1024,( $i = floor( log( $value, 1024 ) ) ) ), 2 ).' '.$unit[$i] . "\n";
			}
		}
		
		return $return;
	}
}

?>