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
<?php echo htmlspecialchars(JText::_("PAYPERDOWNLOADPLUS_PURCHASED_ITEMS"));?>
</div>
<?php
$user = JFactory::getUser();
if(!$user->id)
	echo JText::_("PAYPERDOWNLOADPLUS_LOGIN_TO_VIEW_YOUR_ITEMS");
else
{
?>
<ul class="front_list">
<?php
jimport('joomla.utilities.date');
$last_license = null;
if($this->downloadLinks)
{
	foreach($this->downloadLinks as $download_link)
	{
		?>
		<li>
		<div class="front_title">
		<a href="<?php echo htmlspecialchars($download_link->download_link);?>">
		<?php
		if($download_link->alternate_resource_description)
			echo htmlspecialchars($download_link->alternate_resource_description);
		else
			echo htmlspecialchars($download_link->resource_description . " : " . $download_link->resource_name);
		?>
		</a>
		</div>
		</li>
		<?php
	}
	echo "<div class=\"pagination\">";
	echo $this->pagination->getPagesLinks();
	echo "</div>";
}
else
	echo JText::_("PAYPERDOWNLOADPLUS_NO_DOWNLOAD_LINKS");
?>
</ul>
<?php
}
?>

