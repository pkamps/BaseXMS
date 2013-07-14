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
		$this->renderResult                 = new RenderResult();
		$this->renderResult->output         = $this->getXml();
		$this->renderResult->cssInline      = $this->getCssInline();
		$this->renderResult->cssFileContent = $this->getCssFileContent();
		$this->renderResult->jsFileContent  = $this->getJsFileContent();
		
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
	protected function getCssInline()
	{
		return '';
	}
	
	/**
	 * @return string
	 */
	protected function getCssFileContent()
	{
		return '';
	}
	
	/**
	 * @return string
	 */
	protected function getJsFileContent()
	{
		return '';
	}
}

?>