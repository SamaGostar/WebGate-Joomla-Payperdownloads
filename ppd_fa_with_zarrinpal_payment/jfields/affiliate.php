<?php
/*
 * @package		Joomla.Framework
 * @copyright	Copyright (C) 2005 - 2010 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * @component Phoca Component
 * @copyright Copyright (C) Jan Pavelka www.phoca.cz
 * @license http://www.gnu.org/copyleft/gpl.html GNU General Public License version 2 or later;
 */
defined('_JEXEC') or die();

class JFormFieldAffiliate extends JFormField
{
	protected $type 		= 'Affiliate';

	protected function getInput() {
		
		$db = JFactory::getDBO();;
		$query = "SELECT affiliate_program_id as value, program_name as text FROM #__payperdownloadplus_affiliates_programs ORDER BY program_name";
		$db->setQuery( $query );
		$affiliates = $db->loadObjectList();
		return JHTML::_('select.genericlist',  $affiliates,  $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id );
	}
}
?>