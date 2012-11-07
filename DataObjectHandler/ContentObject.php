<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\DataObjectHandler\DataObjectHandler;

class ContentObject extends DataObjectHandler
{
	function create( $node )
	{
		die('nyi');
				
		$return = false;
		
		if( $this->isValid( $node ) )
		{
			$parentId = (string) $node->attributes()->parentid;
			
			if( $parentId )
			{
				unset( $node->attributes()->parentid );
				
				$nodeStr = $node->toXML();
				
				$query = 'insert node '. $nodeStr .' as last into //node[@id="' . $parentId . '"]';
				
				$result = $this->services->get( 'xmldb' )->execute( $query );
				
				$return = !is_null( $result );
			}
		}
		
		return $return;
	}
	
	function read( $id )
	{
		$query = '//node[@id="' . $id . '"]/content';
		
		$data = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
		
		$data->addAttribute( 'id', $id );
		
		return $data;
	}
	
	function update( $node )
	{
		$return = false;
		
		if( $this->isValid( $node ) )
		{
			// getting id
			$id = (string) $node->attributes()->id;
			
			// update properties
			$properties = $node->properties->asXML();
			$query = 'replace node //node[@id="' . $id . '"]/properties with ' . $properties;
				
			$result = $this->services->get( 'xmldb' )->execute( $query );
			
			$return = !is_null( $result );
		}

		return $return;
	}
	
	function delete( $id )
	{
		die('nyi');
		
		$query = 'delete node //node[@id="'. $id .'"]';
		return $this->services->get( 'xmldb' )->execute( $query );
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