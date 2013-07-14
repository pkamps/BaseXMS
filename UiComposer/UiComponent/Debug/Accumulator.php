<?php 

namespace BaseXMS\UiComposer\UiComponent\Debug;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;

class Accumulator extends HtmlWidget
{
	protected function getXml()
	{
		$data = $this->uiComposer->getServices()->get( 'accumulator')->getData();
		
		$return = '';
		
		if( !empty( $data ) )
		{
			$return .= '
<table class="table table-striped table-hover table-bordered table-condensed">
<caption>Accumulator</caption>
<thead>
	<tr>
		<th>Identifier</th>
		<th>Value</th>
	</tr>
</thead>
<tbody>';
			
			foreach( $data as $identifier => $entry )
			{
				$return .= '<tr>';
		
				$return .= '<td>' . $identifier . '</td>';
				$return .= '<td style="text-align: right">' . number_format( ( $entry[ 'accumulation' ] ) * 1000, 4 ) . ' ms' . '</td>';
		
				$return .= '</tr>';
			}
				
			$return .= '</tbody></table>';
		}

		return $return;
	}
}

?>