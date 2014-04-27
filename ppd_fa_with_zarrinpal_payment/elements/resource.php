<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementResource extends JElement
{
	/**
	 * Element name
	 *
	 * @access	protected
	 * @var		string
	 */
	var	$_name = 'License';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$db = JFactory::getDBO();
		$query = "SELECT resource_license_id as value, 	CONCAT(resource_description, ' ', resource_name) as text FROM #__payperdownloadplus_resource_licenses";
		$db->setQuery( $query );
		$options = $db->loadObjectList( );
		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}
}
