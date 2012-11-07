<?php 

namespace BaseXMS;

class SiteAccessFactory
{
	public static function factory( $context = '', $siteaccesses = null, $application = null )
	{
		$class = '\BaseXMS\SiteAccess';
		
		if( !empty( $siteaccesses ) )
		{
			if( isset( $siteaccesses[ $context ] ) )
			{
				if( class_exists( $siteaccesses[ $context ] ) )
				{
					$class = $siteaccesses[ $context ];
				}
			}
		}
		
		$instance = new $class;
		$instance->init( $application );
		
		return $instance;
	}
}

?>