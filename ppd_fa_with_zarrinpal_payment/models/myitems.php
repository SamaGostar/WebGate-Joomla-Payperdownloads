<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
jimport( 'joomla.application.component.model' );
class ModelPayPerDownloadPlusMyItems extends JModel
{ 
	public function getUserItems($start, $limit)
	{
		$user_id = (int)JFactory::getUser()->id;
		if($user_id)
		{
			$db = JFactory::getDBO();
			$query = "SELECT d.*, r.resource_description, 
				r.alternate_resource_description, r.resource_name 
				FROM #__payperdownloadplus_download_links AS d
				LEFT JOIN #__payperdownloadplus_resource_licenses AS r
				ON d.resource_id = r.resource_license_id 
				WHERE d.user_id = $user_id AND
				(d.expiration_date IS NULL OR d.expiration_date >= NOW()) AND d.payed <> 0";
			$db->setQuery( $query, $start, $limit );
			$downloadLinks = $db->loadObjectList();
			return $downloadLinks;
		}
		return null;
	}
	
	public function getUserTotalItems()
	{
		$user_id = (int)JFactory::getUser()->id;
		if($user_id)
		{
			$db = JFactory::getDBO();
			$query = "SELECT COUNT(*) FROM #__payperdownloadplus_download_links 
				WHERE user_id = $user_id AND
				(expiration_date IS NULL OR expiration_date >= NOW()) AND payed <> 0";
			$db->setQuery( $query );
			return (int)$db->loadResult();
		}
		return 0;
	}
}
?>