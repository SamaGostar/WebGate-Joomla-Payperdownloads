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

class JFormFieldLicense extends JFormField
{
	protected $type 		= 'License';

	protected function getInput() {
		
		$db = JFactory::getDBO();;
		$query = "SELECT license_id as value, license_name as text FROM #__payperdownloadplus_licenses ORDER BY license_name";
		$db->setQuery( $query );
		$licenses = $db->loadObjectList();
		return JHTML::_('select.genericlist',  $licenses,  $this->name, 'class="inputbox"', 'value', 'text', $this->value, $this->id );
	}
}
?>