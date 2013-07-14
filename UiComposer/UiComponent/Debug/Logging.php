<?php 

namespace BaseXMS\UiComposer\UiComponent\Debug;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;

class Logging extends HtmlWidget
{
	protected function getXml()
	{
		$logger    = $this->uiComposer->getServices()->get( 'log' );
		$formatter = new \BaseXMS\Log\Formatter\Html;
		
		$logHtml = '';
		if( $logger->getWriters()->count() )
		{
			$logHtml .= '<div class="accordion" id="#accordion-logging"><caption>Logging</caption>';
			foreach( $logger->getWriters()->toArray() as $index => $writer )
			{
				$logHtml .= '<div class="accordion-group">';
		
				if( $writer instanceof \Zend\Log\Writer\Mock )
				{
					$events = $writer->events;
		
					if( !empty( $events ) )
					{
						$logHtml .= '
						<div class="accordion-heading">
							<a class="accordion-toggle" data-toggle="collapse" data-parent="#accordion-logging" href="#collapse'. $index .'">
							'. $writer->getName() . '
							</a>
						</div>';
		
						$logHtml .= '<div id="collapse'. $index .'" class="accordion-body collapse"><div class="accordion-inner">';
		
						foreach( $events as $event )
						{
							$logHtml .= $formatter->format( $event );
						}
		
						$logHtml .= '</div></div>';
					}
				}
		
				$logHtml .= '</div>';
			}
			$logHtml .= '</div>';
		}
		
		return $logHtml;
	}
}

?>