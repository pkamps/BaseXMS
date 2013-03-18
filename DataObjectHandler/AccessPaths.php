<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\AssertXPathMatch;
use BaseXMS\DataObjectHandler\DataObjectHandler;

class AccessPaths extends DataObjectHandler
{
	protected $nodeOutputFormat =
'<node id="{$x/../@id}">
	{$x}
</node>';
		
	/**
	 * @param int $id
	 */
	public function read( $id, $format = 'xml' )
	{
		$query = 'let $x := //node[@id="' . $id . '"]/accessPaths return '. $this->nodeOutputFormat;
		
		return $this->services->get( 'xmldb' )->execute( $query, $format );
	}
	
	/**
	 * Update the content in the persitent story.
	 * 
	 * @param DOMDocument $doc
	 * @return boolean
	 */
	public function update( \DOMDocument $doc )
	{
		$return = false;

		if( $this->isValid( $doc ) )
		{
			// getting id, new accessPaths and store it
			$id = $doc->firstChild->getAttribute( 'id' );
			$newAccessPath = $doc->saveXML( $doc->firstChild->firstChild );
			$query = 'replace node //node[@id="' . $id . '"]/accessPaths with ' . $newAccessPath;
			$result = $this->services->get( 'xmldb' )->execute( $query );
			
			$return = !is_null( $result );
		}
		
		return $return;
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
			<xs:element ref="accessPaths" minOccurs="1" maxOccurs="1" />
		</xs:sequence>
		
		<xs:attribute name="id" type="xs:integer" use="required"/>
    </xs:complexType>
</xs:element>

<xs:element name="accessPaths">
	<xs:complexType>
		<xs:sequence>
			<xs:element ref="entry" minOccurs="1" maxOccurs="unbounded" />
		</xs:sequence>
	</xs:complexType>
</xs:element>

<xs:element name="entry">
	<xs:complexType mixed="true">
		<xs:attribute name="type" type="xs:string" use="required"/>
		<xs:attribute name="path" type="xs:string" use="required"/>
	</xs:complexType>
</xs:element>


</xs:schema>
XML;
	}
}

?>