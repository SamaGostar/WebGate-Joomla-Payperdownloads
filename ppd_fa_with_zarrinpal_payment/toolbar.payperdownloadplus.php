<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
$task = JRequest::getVar( 'task', '' );
//Do not create toolbar for ajax calls
if($task != "ajaxCall")
{
	$task = JRequest::getVar( 'task', '' );
	$option = JRequest::getVar( 'option', '' );
	$pageToShow = JRequest::getVar( 'adminpage');
	if(!preg_match('/^[a-zA-Z][a-zA-Z0-9]+$/', $pageToShow) || !file_exists((JPATH_COMPONENT.DS.'admin'.DS.$pageToShow.'.php')))
	{
		$pageToShow = 'licenses';
		JRequest::setVar('adminpage', 'licenses');
	}
	//Include de php file for this call
	require_once(JPATH_COMPONENT.DS.'admin'.DS.$pageToShow.'.php');
	$formName = ucfirst($pageToShow).'Form';
	if (class_exists( $formName ))
	{
		//Create the object and create toolbar
		$form = new $formName();
		$form->createToolbar($task, $option);
	}
	else
		echo JText::_('PAYPERDOWNLOADPLUS_INVALID_FORM_1');
}

?>