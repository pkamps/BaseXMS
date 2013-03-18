<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\DataObjectHandler\DataObjectHandler;

class Node extends DataObjectHandler
{

	/**
	 * @param int $parentId
	 * @return boolean|int
	 */
	public function create( $parentId )
	{
		$return = false;
		
		if( $parentId )
		{
			// get new id - probably needs locking
			$query = 'count( //node ) + 1';
			$id = $this->services->get( 'xmldb' )->execute( $query );

			if( $id )
			{
				$query = '
insert node
	<node id="'. $id .'">
	  <accessPaths></accessPaths>
	  <content></content>
	</node>
as last into //node[@id="' . $parentId . '"]';
			
				$result = $this->services->get( 'xmldb' )->execute( $query );
			
				if( !is_null( $result ) )
				{
					$return = (int) $id;
				}
			}
		}
		
		return $return;
	}
	
	function read( $id )
	{
		$returnFormat = '<node id="{$x/@id}" class="{$x/@class}" path="{$x/@path}" parentid="{$x/../@id}">{$x/properties}</node>';
		$query = 'let $x:= //node[@id="' . $id . '"] return if( $x ) then '. $returnFormat . ' else $x';
		
		//echo $query;
		
		return $this->services->get( 'xmldb' )->execute( $query, 'simplexml' );
	}

	/**
	 * @param int $id
	 * @return boolean
	 */
	public function delete( $id )
	{
		$return = false;
		
		$id = (int) $id;

		if( $id )
		{
			$query = 'delete node //node[@id="'. $id .'"]';
			$result = $this->services->get( 'xmldb' )->execute( $query );
			
			if( !is_null( $result ) )
			{
				$return = true;
			}
		}
		
		return $return;
	}

	public function isValid( $node )
	{
		$doc = $this->simpleXmlToDomDocument( $node );
		return $doc->schemaValidateSource( $this->getSchema() );
	}
	
	public function simpleXmlToDomDocument( $simpleXmlElement )
	{
		$domnode = dom_import_simplexml( $simpleXmlElement );
		$dom = new \DOMDocument();
		$domnode = $dom->importNode( $domnode, true );
		$dom->appendChild( $domnode );
		
		return $dom;
	}
	
	protected function getSchema()
	{
		return
<<<XML
<?xml version="1.0" encoding="ISO-8859-1" ?>
<xs:schema xmlns:xs="http://www.w3.org/2001/XMLSchema">
				
<!-- definition of complex elements -->
<xs:element name="node">
	<xs:complexType>
		<xs:sequence>
			<xs:any minOccurs="0" processContents="lax" />
		</xs:sequence>
		
		<xs:attribute name="id" type="xs:string" use="required"/>
		<xs:attribute name="class" type="xs:string" use="required"/>
		<xs:attribute name="parentid" type="xs:string" use="required"/>
		<xs:attribute name="path" type="xs:string" use="required"/>
    </xs:complexType>
</xs:element>

</xs:schema>
XML;
	}
}
?>