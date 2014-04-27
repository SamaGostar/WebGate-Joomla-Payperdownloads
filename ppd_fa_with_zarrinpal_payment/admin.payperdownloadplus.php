<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
/** ensure this file is being included by a parent file */
defined( '_JEXEC' ) or
die( 'Direct Access to this location is not allowed.' );

// Access check.
$version = new JVersion;
if($version->RELEASE >= "2.5")
	if (!JFactory::getUser()->authorise('core.manage', 'com_payperdownloadplus')) 
	{
		return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
	}

$task = JRequest::getCmd( 'task', '' );
$option = JRequest::getCmd( 'option', '' );
$pageToShow = JRequest::getCmd( 'adminpage');
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
	$form->showForm($task, $option);
}
else
	echo JText::_('PAYPERDOWNLOADPLUS_INVALID_FORM_1');

?>