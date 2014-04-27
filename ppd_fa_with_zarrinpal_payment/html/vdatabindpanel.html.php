<?php
/*
* @version 1.0.0
*/
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or
die( 'Direct Access to this location is not allowed.' );

jimport('joomla.html.pane');

class CPanelSeparatorVisualDataBind extends VisualDataBind
{
	var $close;
	var $open;
	var $paneName;
	var $panelName;
	
	function __construct($displayName, $panelName, $open = true, $close = true)
	{
		parent::__construct("", $displayName);
		$this->open = $open;
		$this->close = $close;
		$this->panelName = $panelName;
		$this->showInGrid = false;
		$this->editLinkText = "";
		$this->ignoreToSelect = true;
		$this->ignoreToBind = true;
		$this->useForTextSearch = false;
		$this->paneName = "unnamed-pane";
	}
	
	function setPaneName($paneName)
	{	
		$this->paneName = $paneName;	
	}
	
	function renderNew()
	{
		return $this->renderEdit(null);
	}
	
	function renderEdit(&$row)
	{
		$pane = JPane::getInstance('sliders');
		if($this->open && $this->close)
		{
			return "</table>".
				$pane->endPanel() . 
				$pane->startPanel($this->displayName, 'panel-' . $this->panelName).
				"<table class=\"admintable\">";
		}
		else if($this->open)
		{
			return "</table>".
				$pane->startPane($this->paneName) . 
				$pane->startPanel($this->displayName, 'panel-' . $this->panelName).
				"<table class=\"admintable\">";
		}
		else if($this->close)
		{
			return "</table>".
				$pane->endPanel() . $pane->endPane().
				"<table class=\"admintable\">";
		}
		else
			return "";
	}
	
	function renderValidateJavascript()
	{
		return "";
	}
}

?>