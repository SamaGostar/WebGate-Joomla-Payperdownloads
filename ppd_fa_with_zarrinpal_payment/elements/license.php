<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementLicense extends JElement
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

		$query = 'SELECT license_id, license_name FROM #__payperdownloadplus_licenses';
		$db->setQuery( $query );
		$options = $db->loadObjectList( );


		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'license_id', 'license_name', $value, $control_name.$name );
	}
}
