<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Debug extends HtmlWidget
{
	public function getXml( UiComposer $composer )
	{
		$formatter = new \BaseXMS\Log\Formatter\Html;
		
		// TODO: loop over writers and print out all Mock writer content
		$events = $composer->getServices()->get( 'log' )->getWriters()->top()->events;
		
		$logHtml = '';
		if( !empty( $events ) )
		{
			$logHtml .= '<ul class="log-entries">';
			foreach( $events as $event )
			{
				$logHtml .= $formatter->format( $event );
			}
			$logHtml .= '</ul>';
		}
		
		$content =
		'<div id="debug">
		<h1>Debug</h1>
		' . $logHtml . '
		' . 	$composer->getServices()->get( 'accumulator')->getDataAsHtml() . '
		</div>';
		
		return $content;
	}
	
	public function getCss( UiComposer $composer )
	{
		
		return 
'
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
';
	}
}

?>