<?php 

namespace BaseXMS;

use BaseXMS\Stdlib\DOMDocument;

use BaseXClient\Session,
    BaseXMS\Accumulator,
    BaseXMS\Stdlib\SimpleXMLElement as SimpleXMLElement;

/**
 * @author pek
 * 
 * Wrapper class around the basex provided lib. It's not a singleton because I hope
 * that I can add an instance to a service manager and request that instance from there
 * if needed.
 * 
 * It should handle the connection details, accumulator, error handling
 *
 */
class BaseX
{
	private $session;
	private $connection = array( 'host' => 'localhost',
	                             'port' => 1984,
	                             'user' => 'admin',
	                             'pass' => 'admin',
	                             'db'   => 'base'
	                           );
	private $accumulator;
	private $log;
	
	/**
	 * Takes a given array with connection details
	 * 
	 * @param array $connection
	 * $param Accumulator $accumulator
	 */
	function __construct( $services )
	{
		$this->log = $services->get( 'log' );
		$config    = $services->get( 'config' );

		if( !empty( $config[ 'BaseX' ] ) )
		{
			$this->connection = array_merge( $this->connection, $config[ 'BaseX' ] );
		}

		$this->accumulator = $services->get( 'accumulator' );
	}
	
	/**
	 * @throws Exception
	 * @return boolean
	 */
	public function getSession()
	{
		if( !$this->session )
		{
			try
			{
				$this->session = new Session( $this->connection[ 'host' ],
				                              $this->connection[ 'port' ],
				                              $this->connection[ 'user' ],
				                              $this->connection[ 'pass' ]
				);
			
				$this->session->execute( 'OPEN ' . $this->connection[ 'db' ] );
			}
			catch( \Exception $e )
			{
				throw $e;
			}
		}
		
		return $this->session;
	}
	
	/**
	 * Return formats are: text, xml, simplexml
	 * 
	 * @param sring $query
	 * @param string $returnFormat
	 * @param unknown_type $command
	 * @return unknown
	 */
	public function execute( $query,
	                         $returnFormat = 'text',
			                 $command = 'XQUERY' )
	{

		
		// build query
		$query = $command . ' ' . $query;
		$this->log->debug( $query );
		
		if( $this->accumulator ) $this->accumulator->start( 'baseX' );

		try
		{
			$resultText = $this->getSession()->execute( $query );
		}
		catch( \Exception $e )
		{
			$this->log->err( $e->getMessage() );
			throw $e;
		}
		
		if( $this->accumulator ) $this->accumulator->stop( 'baseX' );
		
		return $this->transformToFormat( $resultText, $returnFormat );
	}

	/**
	 * @param unknown_type $resultText
	 * @param unknown_type $returnFormat
	 * @return string
	 */
	public function transformToFormat( $resultText, $returnFormat = 'text' )
	{
		$return = '';
		
		if( $resultText )
		{
			switch( $returnFormat )
			{
				case 'xml':
				{
					$return = new DOMDocument();
					$return->loadXML( $resultText );
				}
				break;
				
				case 'simplexml':
				{
					$return = new SimpleXMLElement( $resultText );
				}
				break;
				
				case 'json':
				{
					
				}
				break;
				
				// case text
				default:
				{
					$return = $resultText;
				}
			}
		}
		
		return $return;
	}
	
}

?>