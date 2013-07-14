<?php 

namespace BaseXMS\UiComposer\UiComponent\Debug;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\HtmlWidget;

class Debug extends HtmlWidget
{
	protected function getXml()
	{
		return
<<<HTML
<div id="debug">
	<h2>Debugging</h2>
	<div class="row">
		<div class="span4 offset1">
			<include type="accumulator" />
		</div>
		<div class="span8">
			<include type="logging" />
		</div>
	</div>
</div>
HTML;
	}
}

?>