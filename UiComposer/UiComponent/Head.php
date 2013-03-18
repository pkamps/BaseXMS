<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;

class Head extends HtmlWidget
{
	public function getXml()
	{
		return
'<head>
	<title>Default page title</title>
	<include type="inline-css" />
</head>';
	}
}

?>