<?php 

namespace BaseXMS;

/**
 * @author pek
 * 
 *
 */
class FC
{
	static public function link( $link )
	{
		$services = $GLOBALS[ 'application' ]->getServiceLocator();
		$basePath = $services->get( 'Request' )->getBasePath();
		
		// add siteaccess context - I think I should as the siteaccess to build the link
		$siteAccess = $GLOBALS[ 'application' ]->getRouteMatch()->getParam( 'context' );
		$siteAccessUrlPart = $siteAccess ? '/:' . $siteAccess : '';
		
		return $basePath . $siteAccessUrlPart . $link;
	}
	
	static public function assetLink( $link )
	{
		$services = $GLOBALS[ 'application' ]->getServiceLocator();
		$basePath = $services->get( 'Request' )->getBasePath();

		return $basePath . $link;
	}
	
}

?>