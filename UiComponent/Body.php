<?php 

namespace BaseXMS\UiComponent;

use BaseXMS\UiComposer;

class Body extends HtmlWidget
{
	public function getXml( UiComposer $composer )
	{
		return
'<body>
	<include type="content" />
	<include type="debug" />
</body>';
	}
}

?>