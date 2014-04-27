<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class JElementAffiliate extends JElement
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
		$query = "SELECT affiliate_program_id as value, program_name as text FROM #__payperdownloadplus_affiliates_programs ORDER BY program_name";
		$db->setQuery( $query );
		$options = $db->loadObjectList( );
		return JHTML::_('select.genericlist',  $options, ''.$control_name.'['.$name.']', 'class="inputbox"', 'value', 'text', $value, $control_name.$name );
	}
}
