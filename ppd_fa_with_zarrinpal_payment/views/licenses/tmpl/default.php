<?php
/**
 * @component Pay per Download component
 * @author Ratmil Torres
 * @copyright (C) Ratmil Torres
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
**/
defined( '_JEXEC' ) or die( 'Restricted access' );
$root = JURI::root();
?>
<div class="front_title">
<?php echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_MY_LICENSES"));?>
</div>
<?php
$user = JFactory::getUser();
if(!$user->id)
	echo JText::_("PAYPERDOWNLOADPLUS_LOGIN_TO_VIEW_YOUR_LICENSES");
else
{
?>
<ul class="front_list">
<?php
jimport('joomla.utilities.date');
$last_license = null;
if($this->licenses)
{
	foreach($this->licenses as $license)
	{
		
		if($last_license != $license->license_id)
		{
			echo "<li>";
			$url = JRoute::_("index.php?option=com_payperdownloadplus&view=pay&lid=" . (int)$license->license_id);
			echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;" . htmlspecialchars($license->license_name);
			if($license->license_max_downloads > 0)
			{
				echo ",&nbsp;&nbsp;" . JText::_("PAYPERDOWNLOADPLUS_REMAINING_DOWNLOADS") . ":&nbsp;" .  ($license->license_max_downloads - $license->download_hits);
			}
			if($license->expiration_date)
			{
				$date = new JDate($license->expiration_date);
				$format = JText::_("PAYPERDOWNLOADPLUS_DATE_FORMAT");
				if($format == "PAYPERDOWNLOADPLUS_DATE_FORMAT")
					$format = "%B, %d, %Y";
				echo ",&nbsp;&nbsp;" . JText::_("PAYPERDOWNLOADPLUS_EXPIRES") . ":&nbsp;&nbsp;" . $date->toFormat($format);
				if($license->canRenew)
					echo "&nbsp;&nbsp;<a href=\"$url\" class=\"buy_license\" >" .JText::_("PAYPERDOWNLOADPLUS_RENEW_LICENSE") . "</a>";
			}
			else
			{
				echo ",&nbsp;&nbsp;" . JText::_("PAYPERDOWNLOADPLUS_EXPIRES") . ":&nbsp;&nbsp;" . JText::_("PAYPERDOWNLOADPLUS_EXPIRES_NEVER");
			}
			if(count($license->resources))
			{
				echo "<ul class=\"front_end_licenses\">";
				foreach($license->resources as $resource) 
				{
					echo "<li>";
					if($resource->link)
						echo "<a href=\"" . htmlspecialchars($resource->link) . "\">";
					if($resource->alternate_resource_description)
						echo htmlspecialchars($resource->alternate_resource_description);
					else
						echo htmlspecialchars($resource->resource_description . " : " . $resource->resource_name);
					if($resource->link)
						echo "</a>";
					echo "</li>";
				}
				echo "</ul>";
			}
			echo "</li>";	
		}
		?>
		
	<?php
	$last_license = $license->license_id;
	}
	echo "<div class=\"pagination\">";
	echo $this->pagination->getPagesLinks();
	echo "</div>";
}
else
	echo JText::_("PAYPERDOWNLOADPLUS_NO_LICENSES");
?>
</ul>
<?php
}
?>

