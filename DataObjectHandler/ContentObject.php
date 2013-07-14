<?php 

namespace BaseXMS\DataObjectHandler;

use BaseXMS\AssertXPathMatch;
use BaseXMS\DataObjectHandler\DataObjectHandler;

class ContentObject extends DataObjectHandler
{
	protected $nodeOutputFormat =
'<node id="{$x/@id}">
	<path>{let $pathParts := $x/ancestor-or-self::*/accessPaths//entry[@type="main"]/@path/string() return if( count( $pathParts ) > 1 ) then string-join( $pathParts, "/" ) else "/"}</path>
	{$x/content}
</node>';
		
	/**
	 * @param unknown_type $id
	 * @param unknown_type $format
	 */
	public function read( $id, $format = 'text' )
	{
		$query = 'let $x := //node[@id="' . $id . '"] return '. $this->nodeOutputFormat;
		
		return $this->services->get( 'xmldb' )->execute( $query, $format );
	}
	
	/**
	 * Update the content in the persitent story.
	 * 
	 * @param \BaseXMS\Stdlib\DOMDocument $doc
	 * @return boolean
	 */
	public function update( \BaseXMS\Stdlib\DOMDocument $doc )
	{
		$return = false;

		if( !( $doc instanceof BaseXMS\Stdlib\DOMDocument ) )
		{
			if( $this->isValid( $doc ) )
			{
				//TODO: extend rbac and have a simple isGranted method
				if( $this->rbac->isGranted( 'PermissionXML', 'query', new AssertXPathMatch( '/policy', $doc ) ) )
				{
					// getting id, new content and store it
					$id         = $doc->queryToValue( '/node/@id' );
					$newContent = $doc->saveXML( $doc->query( '/node/content' )->item(0) );
					
					$query = 'replace node //node[@id="' . $id . '"]/content with ' . $newContent;
					//echo $query . "\n\n";
										
					$result = $this->services->get( 'xmldb' )->execute( $query );
					
					$return = !is_null( $result );
				}
				else
				{
					$this->services->get( 'log' )->warn( 'User does not have permission to execute "ContentObject::update"' );
				}
			}
			else
			{
				throw new \Exception( 'DOM doc is not validating against content schema' );
			}
		}
		else
		{
			$message = 'No valid doc provided';
			$this->services->get( 'log' )->warn( $message );
			throw new \Exception( $message );
		}

		return $return;
	}

	/**
	 * @param unknown_type $nodeQuery
	 * @param unknown_type $order
	 * @param unknown_type $conditions
	 * @param unknown_type $overridePermissions
	 * @param unknown_type $format
	 */
	public function search( $nodeQuery, $order = '', $conditions = '', $overridePermissions = '' )
	{
		$queryFilter = $nodeQuery;
		$order = '$x/content/sort';
		
		$query =
'<result>
	{for $x in '. $queryFilter
	 .' where '. $this->getPermissionFilterString( $overridePermissions )
	 .' order by '. $order
	 .' return '. $this->nodeOutputFormat
	 .'}
</result>';

		//echo $query;
		
		$result = $this->services->get( 'xmldb' )->execute( $query, 'xml' );
		
		return $result->query( '/result/node' );
	}
	
	/**
	 * @param unknown_type $overridePermissions
	 * @return Ambigous <string, unknown>
	 */
	private function getPermissionFilterString( $overridePermissions )
	{
		$return = '';
		if( $overridePermissions )
		{
			$return = $overridePermissions;
		}
		else
		{
			//TODO: handle empty SearchPermissionFilter
			$filterRole = $this->rbac->getRole( 'SearchPermissionFilter' )->filter;
			$return = '1=2 or $x'. $filterRole;
		}
		
		return $return;
	}
	//
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
			<xs:element ref="content" minOccurs="1" maxOccurs="1" />
		</xs:sequence>
		
		<xs:attribute name="id" type="xs:string" use="required"/>
    </xs:complexType>
</xs:element>

<xs:element name="content">
	<xs:complexType>
		<xs:all>
			<xs:element ref="raw"   minOccurs="0" maxOccurs="1" />
			<xs:element name="sort" minOccurs="0" maxOccurs="1" type="xs:string"/>
			<xs:element name="name" minOccurs="0" maxOccurs="1" type="xs:string"/>
			</xs:all>
	</xs:complexType>
</xs:element>

<xs:element name="raw">
	<xs:complexType mixed="true">
		<xs:sequence>
			<xs:any minOccurs="0" processContents="lax" />
		</xs:sequence>
	</xs:complexType>
</xs:element>

</xs:schema>
XML;
	}
}

?>