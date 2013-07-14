<?php 

namespace BaseXMS;

use BaseXClient\Session;
use BaseXMS\Stdlib\DOMDocument;
use BaseXMS\Accumulator;
use BaseXMS\Stdlib\SimpleXMLElement as SimpleXMLElement;
use BaseXMS\Mvc\BaseXMSService;

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
class BaseX extends BaseXMSService
{
	private $session;
	
	/**
	 * @var array
	 */
	private $connectionDetails = array(
			'host' => 'localhost',
			'port' => 1984,
			'user' => 'admin',
			'pass' => 'admin',
			'db'   => 'base'
	);
		
	/**
	 * @throws Exception
	 * @return boolean
	 */
	public function getSession( $openDb = true )
	{
		if( !$this->session )
		{
			// merge config with default config
			$config = $this->serviceManager->get( 'config' );

			if( !empty( $config[ 'BaseX' ] ) )
			{
				$this->connectionDetails = array_merge( $this->connectionDetails, $config[ 'BaseX' ] );
			}
			
			try
			{
				$this->session = new Session( $this->connectionDetails[ 'host' ],
				                              $this->connectionDetails[ 'port' ],
				                              $this->connectionDetails[ 'user' ],
				                              $this->connectionDetails[ 'pass' ]
				);
			
				// select a DB
				if( $openDb )
				{
					$this->openDb( $this->connectionDetails[ 'db' ] );
				}
			}
			catch( \Exception $e )
			{
				throw $e;
			}
		}
		
		return $this->session;
	}
	
	/**
	 * @param string $name
	 */
	public function openDb( $name )
	{
		if( $name )
		{
			return $this->session->execute( 'OPEN ' . $name );
		}
	}

		
	/**
	 * Return formats are: text, xml, simplexml
	 * 
	 * @param string $query
	 * @param string $returnFormat
	 * @param string $command
	 * @param boolean $openDb
	 * @throws Exception
	 * @return Ambigous <string, unknown_type>
	 */
	public function execute(
			$query,
			$returnFormat = 'text',
			$command = 'XQUERY',
			$openDb = true
	)
	{
		// build query
		$query = $command . ' ' . $query;
		$this->serviceManager->get( 'log' )->debug( $query );
		
		if( $this->serviceManager->has( 'accumulator' ) ) $this->serviceManager->get( 'accumulator' )->start( 'baseX' );

		try
		{
			$resultText = $this->getSession( $openDb )->execute( $query );
		}
		catch( \Exception $e )
		{
			$this->serviceManager->get( 'log' )->err( $e->getMessage() );
			throw $e;
		}
		
		if( $this->serviceManager->has( 'accumulator' ) ) $this->serviceManager->get( 'accumulator' )->stop( 'baseX' );
		
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