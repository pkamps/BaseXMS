<?php 

namespace BaseXMS\UiComposer\UiComponent;

use BaseXMS\UiComposer\UiComposer;
use BaseXMS\UiComposer\UiComponent\Cacheable;
use BaseXMS\UiComposer\RenderResult;

class HtmlWidget extends UiComponent
{
	/* (non-PHPdoc)
	 * @see BaseXMS\UiComponent.UiComponent::render()
	 */
	protected function render()
	{
		$this->renderResult           = new RenderResult();
		$this->renderResult->output   = $this->getXml();
		$this->renderResult->embedCss = $this->getEmbedCss();
		$this->renderResult->jsFiles  = $this->getJsFiles();
		
		return $this;
	}
	
	/**
	 * @return string
	 */
	protected function getXml()
	{
		return '<span>Default from HtmlWidget</span>';
	}

	/**
	 * @return string
	 */
	protected function getEmbedCss()
	{
		return '';
	}
	
	protected function getJsFiles()
	{
		return array();
	}
}

?>