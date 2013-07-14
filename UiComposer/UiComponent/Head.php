<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;

class Head extends HtmlWidget
{
	public function getXml()
	{
		return
'<head>
	<meta charset="utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Default page title</title>
	<include type="css-file" />
	<include type="css-inline" />
</head>';
	}
}

?>