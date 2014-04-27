<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
	defined( '_JEXEC' ) or
	die( 'Direct Access to this location is not allowed.' );
	
	$task = JRequest::getVar("task", "display");
	
	$controller_name = JRequest::getCmd('cont', '');
	
	if($controller_name == '')
	{
		$file = 'controller.php';
		$class_name = 'PayPerDownloadPlusController';
	}
	else
	{
		$file = 'cont_' . $controller_name . '.php';
		$class_name = 'PayPerDownloadPlus' . ucfirst($controller_name) . 'Controller';
	}
	if(file_exists(JPATH_COMPONENT.DS.$file))
	{
		require_once (JPATH_COMPONENT.DS.$file);
		if(class_exists($class_name))
		{
			$controller = new $class_name();
			$controller->execute(JRequest::getVar("task", "display"));
			$controller->redirect();
		}
	}
	
?>