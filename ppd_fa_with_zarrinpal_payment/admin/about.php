<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or
die( 'Direct Access to this location is not allowed.' );

/**
 * @author		Ratmil 
 * http://www.ratmilwebsolutions.com
*/

require_once(JPATH_COMPONENT.DS.'admin'.DS.'ppd.php');
require_once(JPATH_COMPONENT.DS.'data'.DS.'gentable.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdatabind.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'vdatabindmodel.html.php');
require_once(JPATH_COMPONENT.DS.'html'.DS.'about.html.php');

/************************************************************
Class to manage sectors
*************************************************************/
class AboutForm extends PPDForm
{
	/**
	Class constructor
	*/
	function __construct()
	{
		parent::__construct();
		$this->context = 'com_payperdownloadplus';
	}
	
	function getHtmlObject()
	{
		return new AboutHtmlForm();
	}
	
	function doTask($task, $option)
	{
		$this->htmlObject->renderDoc();
	}
	
	function createToolbar($task, $option)
	{
		JToolBarHelper::title( JText::_( 'PAYPERDOWNLOADPLUS_ABOUT_150' ), 'help_header.png' );
	}
}
?>
