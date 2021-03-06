<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;

class Debug extends HtmlWidget
{
	protected function getXml()
	{
		$logger    = $this->uiComposer->getServices()->get( 'log' );
		$formatter = new \BaseXMS\Log\Formatter\Html;
		
		$logHtml = '';
		if( $logger->getWriters()->count() )
		{
			foreach( $logger->getWriters()->toArray() as $writer )
			{
				if( $writer instanceof \Zend\Log\Writer\Mock )
				{
					$events = $writer->events;
		
					if( !empty( $events ) )
					{
						$logHtml .= '<h3>' . $writer->getName() . '</h3>';
						$logHtml .= '<ul class="log-entries">';
						foreach( $events as $event )
						{
							$logHtml .= $formatter->format( $event );
						}
						$logHtml .= '</ul>';
					}
				}
			}
		}
		
		$content =
		'<div id="debug">
		<h1>Debug</h1>
		' . $logHtml . '
		' . 	$this->uiComposer->getServices()->get( 'accumulator')->getDataAsHtml() . '
		</div>';
		
		return $content;
	}
		
	protected function getEmbedCss()
	{
		return
<<<CSS
.log-entries
{
	margin: 0px;
	padding: 0px;
	list-style-type: none;
}
.log-entries .header
{
	border-top: 1px solid grey;
	border-bottom: 1px solid grey;
	padding: 3px;
	background-color: #EDEDED;
}

.log-entries .level1
{
	color: orange;
}

.log-entries .level2
{
	color: orange;
}

.log-entries .level3
{
	color: orange;
}

.log-entries .level4
{
	color: #F5C338;
}

.log-entries .level5
{
	color: orange;
}

.log-entries .level6
{
	color: green;
}

.log-entries .level7
{
	color: blue;
}
CSS;
	}
}

?>